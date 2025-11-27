<?php

namespace Database\Seeders;

use App\Models\LoanType;
use App\Models\LoanTypeNote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoanTypeNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $studentLoan = LoanType::where('loan_type', 'Personal Loan')->first();

        if ($studentLoan) {
            $notes = [
                'Interest rates are subject to credit approval and may vary based on creditworthiness',
                'Processing times are estimates and may vary during peak periods',
                'All loan applications require income verification and credit check',
                'Guarantor requirements may be waived for exceptional credit profiles',
                'Additional documentation may be required for loan amounts above $50,000'
            ];

            foreach ($notes as $note) {
                LoanTypeNote::create([
                    'loan_type_id' => $studentLoan->id,
                    'note' => $note
                ]);
            }
        }
    }
}
