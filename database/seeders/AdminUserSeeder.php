<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = [
            'full_name' => 'Admin',
            'email_address' => 'ZK4QH@example.com',
            'mobile_number' => '1234567890',
            'password' => bcrypt('password'),
            'user_type_id' => 1,
            'is_active' => true,
            'org' => 'org1',
            'nic' => '123456789V',
            'branch_id' => 1,
            'department_id' => 1
        ];

        User::updateOrCreate(
            ['nic' => $adminUser['nic']],   // Search condition
            $adminUser                     // Values to update
        );

    }
}
