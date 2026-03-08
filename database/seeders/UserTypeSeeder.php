<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_types')->insert([
            [
                'user_type' => 'SuperAdmin',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'org' => 'org1'
            ],
            [
                'user_type' => 'Admin',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'org' => 'org1'
            ],
            [
                'user_type' => 'Manager',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'org' => 'org2'
            ],
            [
                'user_type' => 'User',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'org' => 'org2'
            ],
            [
                'user_type' => 'Member',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'org' => 'org2'
            ],
        ]);
    }
}
