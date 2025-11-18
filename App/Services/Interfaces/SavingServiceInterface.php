<?php

namespace App\Services\Interfaces;


interface SavingServiceInterface
{
    public function createSavingAccount(array $data);

    public function updateSavingAccount(array $data);

    public function getSavingAccounts(array $data);

}
