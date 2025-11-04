<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed master data for organization hierarchy
        $this->call([
            DirektoratSeeder::class,
            DivisiSeeder::class,
            UnitSeeder::class,
            JabatanSeeder::class,
            PosisiSeeder::class,
        ]);
    }
}
