<?php

namespace App\Services;

use App\Models\LearningPlanProposal;
use App\Models\LearningPlanRecommendation;
use App\Models\LearningRecommendation;
use App\Models\LearningTarget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PlanApprovalService
{
    /**
     * Apply an approved proposal:
     * - Upsert LearningTarget on scope (direktorat/divisi)
     * - Copy recommendations into learning_recommendations
     */
    public function apply(LearningPlanProposal $proposal, int $approverId): void
    {
        if ($proposal->status !== 'submitted') {
            throw new \InvalidArgumentException('Proposal must be in submitted state.');
        }

        DB::transaction(function () use ($proposal, $approverId) {
            // Mark approved
            $proposal->status = 'approved';
            $proposal->approved_by = $approverId;
            $proposal->approved_at = Carbon::now();
            $proposal->save();

            // Upsert target if provided
            if ($proposal->target_minutes && $proposal->target_minutes > 0) {
                if ($proposal->scope_type === 'unit' && $proposal->only_subordinate_jabatans) {
                    // Apply to subordinate jabatans within the unit (unit+jabatan composite targets)
                    $managerJabatanLevel = optional($proposal->proposer->karyawan->jabatan)->level;
                    // Remove any existing unit-level target for this period+unit to avoid broad fallback
                    LearningTarget::where('period_id', $proposal->period_id)
                        ->where('unit_id', $proposal->scope_id)
                        ->whereNull('jabatan_id')
                        ->whereNull('karyawan_id')
                        ->delete();

                    $subordinateJabatans = \App\Models\Jabatan::query()
                        ->when(isset($managerJabatanLevel), function($q) use ($managerJabatanLevel) {
                            // In this system: smaller number = lebih rendah; ambil level yang lebih kecil dari manager
                            return $q->where('level', '<', $managerJabatanLevel);
                        })
                        ->pluck('id');

                    foreach ($subordinateJabatans as $jabatanId) {
                        $attrs = [
                            'period_id' => $proposal->period_id,
                            'karyawan_id' => null,
                            'jabatan_id' => $jabatanId,
                            'unit_id' => $proposal->scope_id,
                            'divisi_id' => null,
                            'direktorat_id' => null,
                        ];
                        $target = LearningTarget::firstOrNew($attrs);
                        $target->target_minutes = $proposal->target_minutes;
                        $target->save();
                    }
                } else {
                    $attrs = [
                        'period_id' => $proposal->period_id,
                        'karyawan_id' => null,
                        'jabatan_id' => null,
                        'unit_id' => $proposal->scope_type === 'unit' ? $proposal->scope_id : null,
                        'divisi_id' => $proposal->scope_type === 'divisi' ? $proposal->scope_id : null,
                        'direktorat_id' => $proposal->scope_type === 'direktorat' ? $proposal->scope_id : null,
                    ];
                    $target = LearningTarget::firstOrNew($attrs);
                    $target->target_minutes = $proposal->target_minutes;
                    $target->save();
                }
            }

            // Copy recommendations to applied table
            if ($proposal->scope_type === 'unit' && $proposal->only_subordinate_jabatans) {
                // Remove any previous recs tied to this proposal (idempotency)
                LearningRecommendation::where('approved_proposal_id', $proposal->id)->delete();

                $managerJabatanLevel = optional($proposal->proposer->karyawan->jabatan)->level;
                $subordinateJabatans = \App\Models\Jabatan::query()
                    ->when(isset($managerJabatanLevel), function($q) use ($managerJabatanLevel) {
                        // Note: in this system, smaller level number means lower rank; manager wants lower-than-self
                        return $q->where('level', '<', $managerJabatanLevel);
                    })
                    ->pluck('id');

                foreach ($proposal->recommendations as $rec) {
                    foreach ($subordinateJabatans as $jabatanId) {
                        LearningRecommendation::updateOrCreate(
                            [
                                'period_id' => $proposal->period_id,
                                'scope_type' => 'unit',
                                'scope_id' => $proposal->scope_id,
                                'jabatan_id' => $jabatanId,
                                'title' => $rec->title,
                            ],
                            [
                                'url' => $rec->url,
                                'platform_id' => $rec->platform_id,
                                'approved_proposal_id' => $proposal->id,
                                'created_by' => $approverId,
                            ]
                        );
                    }
                }
            } else {
                foreach ($proposal->recommendations as $rec) {
                    LearningRecommendation::updateOrCreate(
                        [
                            'period_id' => $proposal->period_id,
                            'scope_type' => $proposal->scope_type,
                            'scope_id' => $proposal->scope_id,
                            'title' => $rec->title,
                        ],
                        [
                            'url' => $rec->url,
                            'platform_id' => $rec->platform_id,
                            'approved_proposal_id' => $proposal->id,
                            'created_by' => $approverId,
                        ]
                    );
                }
            }
        });
    }
}
