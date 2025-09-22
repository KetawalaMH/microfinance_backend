<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_type_id',
        'full_name',
        'email_address',
        'mobile_number',
        // 'nic_number',
        // 'address_line1',
        // 'address_line2',
        // 'city_id',
        // 'district_id',
        // 'province_id',
        // 'login_type',
        'password',
        // 'normal_password',
        // 'social_password',
        //'push_id',
        //'os_type',
        // 'memory_count',
        // 'moment_count',
        // 'storage',
        // 'used_storage',
        'is_active',
        'created_at',
        'updated_at',
        'bank_id'
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims()
    {
        return [
            'bank_id' => $this->bank_id,
            'user_type_id' => $this->user_type_id,
        ];
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }
    public function bankId()
    {
        return $this->belongsTo(BankProfile::class, 'bank_id');
    }
}
