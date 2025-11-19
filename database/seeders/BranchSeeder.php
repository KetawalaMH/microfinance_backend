<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            ['branch' => 'Branch 1', 'address' => 'Address 1', 'branch_code' => 'BRC1', 'is_active' => true],
            ['branch' => 'Branch 2', 'address' => 'Address 1', 'branch_code' => 'BRC2', 'is_active' => true],
            ['branch' => 'Branch 3', 'address' => 'Address 1', 'branch_code' => 'BRC3', 'is_active' => true],
            ['branch' => 'Branch 4', 'address' => 'Address 1', 'branch_code' => 'BRC4', 'is_active' => true],
            ['branch' => 'Branch 5', 'address' => 'Address 1', 'branch_code' => 'BRC5', 'is_active' => true],

        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(
                ['branch_code' => $branch['branch_code']], // unique field to match existing rows
                $branch
            );
        }

    }
}
