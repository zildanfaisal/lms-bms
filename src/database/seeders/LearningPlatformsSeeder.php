<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LearningPlatform;

class LearningPlatformsSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            ['name' => 'Google Drive', 'type' => 'internal', 'url' => 'https://drive.google.com', 'description' => 'Internal materials'],
            ['name' => 'MySkill', 'type' => 'external', 'url' => 'https://myskill.id', 'description' => 'External LMS'],
        ];

        foreach ($platforms as $p) {
            LearningPlatform::firstOrCreate(['name' => $p['name']], $p);
        }
    }
}
