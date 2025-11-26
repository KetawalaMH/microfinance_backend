<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'amount',
        'loan_type_id',
        'borrower_id',
        'branch_id',
        'status',
        'purpose',
        'start_date',
        'number_of_installments',
        'installment_amount',
        'guranter1_id',
        'guranter2_id',
        'next_installment_date',
        'last_installment_date',
        'remaining_installments',
        'paid_amount',
        'remaining_amount',
        'documents',
        'aprroved_by',
        'submitted_by',
        'approved_date',
        'repayment_start_date',
        'is_active',
        'duration',
        'interest',
    ];

    public function loanType()
    {
        return $this->belongsTo(LoanType::class, 'loan_type_id');
    }

    public function borrower()
    {
        return $this->belongsTo(Member::class, 'borrower_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function gurantor1()
    {
        return $this->belongsTo(Member::class, 'guranter1_id');
    }

    public function gurantor2()
    {
        return $this->belongsTo(Member::class, 'guranter2_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function documents()
    {
        return $this->hasMany(LoanDocument::class);
    }
}
