<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentLog extends Model
{
    protected $fillable = [
        'loan_id',
        'amount',
        'paid_date',
        'month',
        'due_date',
        'recorded_by',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class);
    }
}
