<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nic',
        'full_name',
        'email',
        'mobile_number',
        'address',
        'dob',
        'occupation',
        'is_active',
        'member_type',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'dob' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: A customer belongs to a member type.
     */
    public function memberType()
    {
        return $this->belongsTo(MemberType::class, 'member_type');
    }
}
