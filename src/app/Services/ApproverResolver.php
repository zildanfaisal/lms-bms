<?php

namespace App\Services;

use App\Models\Karyawan;
use Illuminate\Support\Collection;

class ApproverResolver
{
    /**
     * Determine eligible approvers for the owner based on hierarchy rules.
     * Returns a collection of Karyawan (nearest higher level in Unit → Divisi → Direktorat fallback).
     */
    public function for(Karyawan $owner): Collection
    {
        $ownerLevel = optional($owner->jabatan)->level;
        if (!$ownerLevel) {
            return collect();
        }

        // 1) Same Unit
        $unitApprovers = $this->findApprovers(
            level: $ownerLevel,
            direktoratId: $owner->direktorat_id,
            divisiId: $owner->divisi_id,
            unitId: $owner->unit_id
        );
        if ($unitApprovers->isNotEmpty()) {
            return $unitApprovers;
        }

        // 2) Same Divisi
        $divisiApprovers = $this->findApprovers(
            level: $ownerLevel,
            direktoratId: $owner->direktorat_id,
            divisiId: $owner->divisi_id,
            unitId: null
        );
        if ($divisiApprovers->isNotEmpty()) {
            return $divisiApprovers;
        }

        // 3) Same Direktorat
        $direktoratApprovers = $this->findApprovers(
            level: $ownerLevel,
            direktoratId: $owner->direktorat_id,
            divisiId: null,
            unitId: null
        );
        if ($direktoratApprovers->isNotEmpty()) {
            return $direktoratApprovers;
        }

        return collect();
    }

    /**
     * Check if the given approver is an eligible approver for the owner
     * based on the nearest-higher rule within Unit → Divisi → Direktorat.
     */
    public function isEligibleApprover(Karyawan $owner, Karyawan $approver): bool
    {
        return $this->for($owner)->pluck('id')->contains($approver->id);
    }

    /**
     * Find higher-level karyawan in the given organizational scope.
     */
    protected function findApprovers(int $level, int $direktoratId = null, int $divisiId = null, int $unitId = null): Collection
    {
        $query = Karyawan::query()
            ->with('jabatan')
            ->whereHas('jabatan', fn($q) => $q->where('level', '>', $level));

        if ($direktoratId) $query->where('direktorat_id', $direktoratId);
        if ($divisiId) $query->where('divisi_id', $divisiId);
        if ($unitId) $query->where('unit_id', $unitId);

        $candidates = $query->get();

        // Choose nearest higher level(s)
        if ($candidates->isEmpty()) return collect();

        $minDiff = $candidates->map(fn($k) => $k->jabatan->level - $level)->min();
        return $candidates->filter(fn($k) => ($k->jabatan->level - $level) === $minDiff)->values();
    }
}
