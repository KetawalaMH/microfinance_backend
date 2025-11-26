<?php

namespace Database\Seeders;

use App\Models\LoanType;
use App\Models\LoanTypeNote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoanTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $loanTypes = [
            [
                'loan_type' => 'Student Loan',
                'description' => 'Educational financing for tuition, books, and living expenses',
                'min_interest_rate' => 3.5,
                'max_interest_rate' => 7.8,
                'panelty' => 5,
                'panelty_type' => 'percentage',
                'max_amount' => 2500000,
                'min_amount' => 5000,
                'duration' => 5,
                'duration_type' => 'years',
                'processing_fee' => 1000,
                'number_of_gaurantors' => 2,
                'min_installments' => 5,
                'max_installments' => 60,
                'term' => 'monthly',
                'min_processing_dates' => 7,
                'max_processing_dates' => 10,
            ],
            [
                'loan_type' => 'Auto Loan',
                'description' => 'Secured loans for purchasing new or used vehicles',
                'min_interest_rate' => 3.5,
                'max_interest_rate' => 7.8,
                'panelty' => 5,
                'panelty_type' => 'percentage',
                'max_amount' => 2500000,
                'min_amount' => 5000,
                'duration' => 5,
                'duration_type' => 'years',
                'processing_fee' => 1000,
                'number_of_gaurantors' => 2,
                'min_installments' => 5,
                'max_installments' => 60,
                'term' => 'monthly',
                'min_processing_dates' => 7,
                'max_processing_dates' => 10,
            ],
            [
                'loan_type' => 'Home Improvements',
                'description' => 'Loans for renovations, repairs, and home upgrades',
                'min_interest_rate' => 3.5,
                'max_interest_rate' => 7.8,
                'panelty' => 5,
                'panelty_type' => 'percentage',
                'max_amount' => 2500000,
                'min_amount' => 5000,
                'duration' => 5,
                'duration_type' => 'years',
                'processing_fee' => 1000,
                'number_of_gaurantors' => 2,
                'min_installments' => 5,
                'max_installments' => 60,
                'term' => 'monthly',
                'min_processing_dates' => 7,
                'max_processing_dates' => 10,
            ],
            [
                'loan_type' => 'Personal Loan',
                'description' => 'Unsecured loans for personal expenses and debt consolidation',
                'min_interest_rate' => 3.5,
                'max_interest_rate' => 7.8,
                'panelty' => 5,
                'panelty_type' => 'percentage',
                'max_amount' => 2500000,
                'min_amount' => 5000,
                'duration' => 5,
                'duration_type' => 'years',
                'processing_fee' => 1000,
                'number_of_gaurantors' => 2,
                'min_installments' => 5,
                'max_installments' => 60,
                'term' => 'monthly',
                'min_processing_dates' => 7,
                'max_processing_dates' => 10,
                'notes' => [
                    'Interest rates are subject to credit approval and may vary based on creditworthiness',
                    'Processing times are estimates and may vary during peak periods',
                    'All loan applications require income verification and credit check',
                    'Guarantor requirements may be waived for exceptional credit profiles',
                    'Additional documentation may be required for loan amounts above $50,000'
                ],
            ],
        ];

        foreach ($loanTypes as $loanTypeData) {

            // Remove notes before creating LoanType
            $notes = $loanTypeData['notes'] ?? [];
            unset($loanTypeData['notes']);

            // Create or update loan type
            $loanType = LoanType::updateOrCreate(
                ['loan_type' => $loanTypeData['loan_type']],
                $loanTypeData
            );

            //drop existing notes
            LoanTypeNote::where('loan_type_id', $loanType->id)->delete();

            // Insert notes into loan_type_notes table
            foreach ($notes as $noteText) {
                LoanTypeNote::create([
                    'loan_type_id' => $loanType->id,
                    'note' => $noteText,
                ]);
            }
        }


    }
}
