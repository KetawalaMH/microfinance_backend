<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_type',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        "user_type"=>"string",
        "is_active"=>"integer",
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}

