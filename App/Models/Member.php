<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'members';

    protected $fillable = [
        'NIC',
        'full_name',
        'email_address',
        'mobile_number',
        'address',
        'dob',
        'occupation',
        'is_active',
        'member_type_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'dob' => 'date',
    ];

    /**
     * Relationship: A member belongs to a member type.
     */
    public function memberType()
    {
        return $this->belongsTo(MemberType::class, 'member_type_id');
    }
}
