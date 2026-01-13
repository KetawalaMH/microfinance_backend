<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanPrediction extends Model
{
    protected $fillable = [
        'loan_id',
        'loan_eligibility',
        'repayment_capacity',
        'fraud_risk',
        'optimal_tenure',
        'recommended_loan_amount'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
