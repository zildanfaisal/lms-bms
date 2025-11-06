<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\LearningTarget;
use Illuminate\Support\Collection;

class TargetResolver
{
    protected array $byKaryawan = [];
    protected array $byJabatan = [];
    protected array $byUnit = [];
    protected array $byDivisi = [];
    protected array $byDirektorat = [];
    protected ?int $loadedPeriodId = null;

    /**
     * Load targets for a period into memory (latest wins per scope key).
     */
    protected function loadForPeriod(int $periodId): void
    {
        if ($this->loadedPeriodId === $periodId) return;

        $this->byKaryawan = $this->byJabatan = $this->byUnit = $this->byDivisi = $this->byDirektorat = [];
        $targets = LearningTarget::where('period_id', $periodId)
            ->orderByDesc('id')
            ->get(['id','period_id','karyawan_id','jabatan_id','unit_id','divisi_id','direktorat_id','target_minutes']);

        foreach ($targets as $t) {
            if ($t->karyawan_id) { $this->byKaryawan[$t->karyawan_id] = (int)$t->target_minutes; }
            if ($t->jabatan_id) { $this->byJabatan[$t->jabatan_id] = (int)$t->target_minutes; }
            if ($t->unit_id) { $this->byUnit[$t->unit_id] = (int)$t->target_minutes; }
            if ($t->divisi_id) { $this->byDivisi[$t->divisi_id] = (int)$t->target_minutes; }
            if ($t->direktorat_id) { $this->byDirektorat[$t->direktorat_id] = (int)$t->target_minutes; }
        }

        $this->loadedPeriodId = $periodId;
    }

    /**
     * Resolve effective target minutes for a given karyawan and period.
     * Precedence: karyawan > jabatan > unit > divisi > direktorat.
     */
    public function for(Karyawan $karyawan, int $periodId): ?int
    {
        $this->loadForPeriod($periodId);

        if (isset($this->byKaryawan[$karyawan->id])) return $this->byKaryawan[$karyawan->id];
        if ($karyawan->jabatan_id && isset($this->byJabatan[$karyawan->jabatan_id])) return $this->byJabatan[$karyawan->jabatan_id];
        if ($karyawan->unit_id && isset($this->byUnit[$karyawan->unit_id])) return $this->byUnit[$karyawan->unit_id];
        if ($karyawan->divisi_id && isset($this->byDivisi[$karyawan->divisi_id])) return $this->byDivisi[$karyawan->divisi_id];
        if ($karyawan->direktorat_id && isset($this->byDirektorat[$karyawan->direktorat_id])) return $this->byDirektorat[$karyawan->direktorat_id];
        return null;
    }

    /**
     * Resolve for a collection of karyawan at once; returns map [karyawan_id => minutes|null].
     */
    public function forMany(Collection $karyawans, int $periodId): array
    {
        $this->loadForPeriod($periodId);
        $result = [];
        foreach ($karyawans as $k) {
            if (!$k instanceof Karyawan) continue;
            $result[$k->id] = $this->for($k, $periodId);
        }
        return $result;
    }
}
