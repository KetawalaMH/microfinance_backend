<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'bank_id',
        'user_id',
        'email_address',
        'token',
        'sent_by',
        'is_active',
        'created_at',
        'updated_at'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
    public function bankId(): BelongsTo
    {
        return $this->belongsTo(BankProfile::class, 'sent_by');
    }
}
