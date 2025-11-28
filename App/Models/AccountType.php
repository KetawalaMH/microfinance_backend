<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    protected $fillable = [
        'type_name',
        'type_description',
        'time_duration',
        'interest_rate',
        'age_limit',
        'minimum_amount',
        'maximum_amount',
        'minimum_amount',
        'panalty_rate',
        'tax_rate',
        'panalty_duration',
        'panalty_amount',
        'created_by',
        'updated_by',
        'is_active',
        'created_at',
        'updated_at',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
