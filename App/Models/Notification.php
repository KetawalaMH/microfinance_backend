<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
        'meta_data'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'meta_data' => 'array',
    ];

    // Relationship
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
