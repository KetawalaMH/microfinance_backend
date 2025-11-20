<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordChangeLog extends Model
{
    protected $fillable = [
        'user_id',
        'old_password',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
