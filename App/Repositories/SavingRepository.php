<?php

namespace App\Repositories;

use App\Models\SavingAccount;
use App\Repositories\Interfaces\SavingRepositoryInterface;

class SavingRepository implements SavingRepositoryInterface
{
    public function createSavingAccount(array $data)
    {
        return SavingAccount::create($data);
    }
}
