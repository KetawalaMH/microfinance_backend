<?php

namespace App\Services;

use App\Models\SavingAccount;
use App\Repositories\Interfaces\SavingAccountRepositoryInterface;
use App\Repositories\Interfaces\SavingRepositoryInterface;
use App\Services\Interfaces\SavingServiceInterface;

class SavingService implements SavingServiceInterface
{
    private $repository;

    public function __construct(SavingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createSavingAccount(array $data)
    {

        return $this->repository->createSavingAccount($data);
    }
}
