<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingAccount extends Model
{
    protected $fillable = [
        'id',
        'account_number',
        'member_id',
        'account_type_id',
        'created_by',
        'initial_deposite',
        'current_balance',
        'NIC_No',
        'phone_number',
        'email_address',
        'address',
        'status',
        'documentUrls',
        'bank_id',
        'NIC_doc',
        'proof_of_address',
        'deposite_slip',
        'application_form',
        'is_active',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function accountType()
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }

}
