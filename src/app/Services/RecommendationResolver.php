<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\LearningRecommendation;
use Illuminate\Support\Collection;

class RecommendationResolver
{
    protected array $byUnit = [];
    protected array $byUnitJabatan = [];
    protected array $byDivisi = [];
    protected array $byDirektorat = [];
    protected ?int $loadedPeriodId = null;

    /**
     * Load applied recommendations for a period into memory.
     */
    protected function loadForPeriod(int $periodId): void
    {
        if ($this->loadedPeriodId === $periodId) return;

    $this->byUnit = $this->byUnitJabatan = $this->byDivisi = $this->byDirektorat = [];

        $recs = LearningRecommendation::where('period_id', $periodId)
            // Urutkan sesuai urutan input awal (id terkecil lebih dulu)
            ->orderBy('id')
            ->get(['id','period_id','scope_type','scope_id','jabatan_id','title','url','platform_id','approved_proposal_id','target_minutes']);

        foreach ($recs as $r) {
            switch ($r->scope_type) {
                case 'unit':
                    if ($r->jabatan_id) {
                        $this->byUnitJabatan[$r->scope_id.'-'.$r->jabatan_id][] = $r;
                    } else {
                        $this->byUnit[$r->scope_id][] = $r;
                    }
                    break;
                case 'divisi':
                    $this->byDivisi[$r->scope_id][] = $r;
                    break;
                case 'direktorat':
                    $this->byDirektorat[$r->scope_id][] = $r;
                    break;
            }
        }

        $this->loadedPeriodId = $periodId;
    }

    /**
     * Resolve recommendations for a given karyawan and period.
     * Precedence: unit > divisi > direktorat. Dedupe by title (narrower scope wins).
     *
    * @return array<int, array{id:int,title:string,url:?string,platform_id:?int,scope_type:string,scope_id:int,jabatan_id:?int,approved_proposal_id:?int}>
     */
    public function for(Karyawan $karyawan, int $periodId): array
    {
        $this->loadForPeriod($periodId);

        $result = [];
        $seenTitles = [];

        // Helper to push items ensuring unique by title
        $push = function ($items) use (&$result, &$seenTitles) {
            if (!$items) return;
            foreach ($items as $r) {
                $titleKey = mb_strtolower(trim((string)$r->title));
                if ($titleKey === '') continue;
                if (isset($seenTitles[$titleKey])) continue; // keep narrower scope
                $seenTitles[$titleKey] = true;
                $result[] = [
                    'id' => (int)$r->id,
                    'title' => (string)$r->title,
                    'url' => $r->url,
                    'platform_id' => $r->platform_id,
                    'scope_type' => (string)$r->scope_type,
                    'scope_id' => (int)$r->scope_id,
                    'jabatan_id' => $r->jabatan_id,
                    'approved_proposal_id' => $r->approved_proposal_id,
                    'target_minutes' => $r->target_minutes,
                ];
            }
        };

        if ($karyawan->unit_id && $karyawan->jabatan_id && isset($this->byUnitJabatan[$karyawan->unit_id.'-'.$karyawan->jabatan_id])) {
            $push($this->byUnitJabatan[$karyawan->unit_id.'-'.$karyawan->jabatan_id]);
        }
        if ($karyawan->unit_id && isset($this->byUnit[$karyawan->unit_id])) {
            $push($this->byUnit[$karyawan->unit_id]);
        }
        if ($karyawan->divisi_id && isset($this->byDivisi[$karyawan->divisi_id])) {
            $push($this->byDivisi[$karyawan->divisi_id]);
        }
        if ($karyawan->direktorat_id && isset($this->byDirektorat[$karyawan->direktorat_id])) {
            $push($this->byDirektorat[$karyawan->direktorat_id]);
        }

        return $result;
    }
}
