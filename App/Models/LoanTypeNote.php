<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanTypeNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_type_id',
        'note',
    ];

    /**
     * Relationship: Each note belongs to one loan type.
     */
    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }
}
