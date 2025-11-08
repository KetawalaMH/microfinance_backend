<?php

namespace App\Services\Interfaces;

use App\Repositories\Interfaces\BankRepositoryInterface;

interface BankServiceInterface
{

    public function createBankProfile(array $data);

}
