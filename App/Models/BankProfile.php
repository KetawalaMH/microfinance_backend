<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankProfile extends Model
{
    //

    protected $fillable =[
        'bank_name',
        'location',
        'owner_id',
        'is_active',
        'created_at',
        'updated_at',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}

