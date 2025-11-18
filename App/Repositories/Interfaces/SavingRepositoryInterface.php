<?php

namespace App\Repositories\Interfaces;

use App\Models\SavingAccount;

interface SavingRepositoryInterface
{
    public function createSavingAccount(array $data);

    public function updateSavingAccount(array $data);

    public function getSavingAccounts(array $data);
}
