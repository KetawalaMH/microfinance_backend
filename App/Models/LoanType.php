<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    protected $fillable = [
        'loan_type',
        'description',
        'min_interest_rate',
        'max_interest_rate',
        'panelty',
        'panelty_type',
        'max_amount',
        'min_amount',
        'is_active',
        'processing_fee',
        'number_of_gaurantors',
        'min_installments',
        'max_installments',
        'term',
        'min_processing_dates',
        'max_processing_dates',
        'duration_type',
        'duration'
    ];

    public function notes()
    {
        return $this->hasMany(LoanTypeNote::class);
    }
}
