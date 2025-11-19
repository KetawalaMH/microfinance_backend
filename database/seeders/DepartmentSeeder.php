<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['department' => 'Department 1', 'is_active' => true],
            ['department' => 'Department 2', 'is_active' => true],
            ['department' => 'Department 3', 'is_active' => true],
            ['department' => 'Department 4', 'is_active' => true],
            ['department' => 'Department 5', 'is_active' => true],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['department' => $department['department']],
                ['is_active' => $department['is_active']]
            );
        }
    }
}
