<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanDocument extends Model
{
    protected $fillable = [
        'loan_id',
        'document_path',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
