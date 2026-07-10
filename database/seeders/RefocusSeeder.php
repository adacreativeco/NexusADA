<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class RefocusSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'Marketing',
            'Corporate Communications',
            'Project Management',
            'Brand Management',
            'Media Management',
        ];

        foreach ($departments as $name) {
            Department::updateOrCreate(['name' => $name]);
        }
    }
}
