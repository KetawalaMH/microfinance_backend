<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    protected $fillable = [
        'done_by',
        'title',
        'description',
        'account_id',
        'member_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'done_by');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function account()
    {
        return $this->belongsTo(SavingAccount::class, 'account_id');
    }
}
