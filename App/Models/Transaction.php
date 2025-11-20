<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    private $fillable = [
        'account_id',
        'amount',
        'title',
        'description',
        'recorded_by',
        'confirmed_by',
        'type',
        'is_confirmed',
        'is_active',
        'balance',
    ];

    public function account()
    {
        return $this->belongsTo(SavingAccount::class, 'account_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
