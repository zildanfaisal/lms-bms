<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LearningPeriod;
use Carbon\Carbon;

class LearningPeriodsSeeder extends Seeder
{
    public function run(): void
    {
        // Seed current and next two months
        $start = Carbon::today()->startOfMonth();
        for ($i = -1; $i <= 2; $i++) {
            $s = (clone $start)->addMonths($i);
            $e = (clone $s)->endOfMonth();
            $code = $s->format('Y-m');
            LearningPeriod::firstOrCreate(
                ['code' => $code],
                ['name' => $s->isoFormat('MMMM YYYY'), 'starts_at' => $s->toDateString(), 'ends_at' => $e->toDateString(), 'is_locked' => false]
            );
        }
    }
}
