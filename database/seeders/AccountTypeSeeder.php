<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Develop account type seeder with four account types
        $accountTypes = [
            [
                'type_name' => 'Savings Account',
                'type_description' => 'A basic savings account with competitive interest for individuals.',
                'interest_rate' => 3.50,
                'penalty_rate' => 1.00,
                'penalty_amount' => 200.00,
                'tax_rate' => 5.00,
                'penalty_duration' => 30,
                'age_limit' => 18,
                'duration' => 365,
                'minimum_balance' => 1000.00,
                'maximum_balance' => 500000.00,
                'minimum_amount' => 500.00,
                'created_by' => 1,
                'updated_by' => 1,
                'is_active' => true,
            ],
            [
                'type_name' => 'Fixed Deposit',
                'type_description' => 'Long-term fixed deposit account offering higher interest rates.',
                'interest_rate' => 8.25,
                'penalty_rate' => 2.50,
                'penalty_amount' => 1000.00,
                'tax_rate' => 10.00,
                'penalty_duration' => 90,
                'age_limit' => 18,
                'duration' => 365,
                'minimum_balance' => 5000.00,
                'maximum_balance' => 200000.00,
                'minimum_amount' => 5000.00,
                'created_by' => 1,
                'updated_by' => 1,
                'is_active' => true,
            ],
            [
                'type_name' => 'Senior Citizen Savings',
                'type_description' => 'Special savings account designed for senior citizens with higher interest rates.',
                'interest_rate' => 6.50,
                'penalty_rate' => 1.00,
                'penalty_amount' => 250.00,
                'tax_rate' => 3.00,
                'penalty_duration' => 30,
                'age_limit' => 60,
                'duration' => 365,
                'minimum_balance' => 2000.00,
                'maximum_balance' => 100000.00,
                'minimum_amount' => 1000.00,
                'created_by' => 1,
                'updated_by' => 1,
                'is_active' => true,
            ],
            [
                'type_name' => 'Student Savings Account',
                'type_description' => 'Account for students with zero maintenance charges and flexible deposits.',
                'interest_rate' => 4.00,
                'penalty_rate' => 0.50,
                'penalty_amount' => 100.00,
                'tax_rate' => 2.00,
                'penalty_duration' => 15,
                'age_limit' => 16,
                'duration' => 365,
                'minimum_balance' => 0.00,
                'maximum_balance' => 250000.00,
                'minimum_amount' => 100.00,
                'created_by' => 1,
                'updated_by' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($accountTypes as $accountType) {
            AccountType::updateOrCreate(
                ['type_name' => $accountType['type_name']],
                $accountType
            );
        }
    }
}
