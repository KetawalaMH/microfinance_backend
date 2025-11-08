<?php

namespace App\Services;

use App\Models\BankProfile;
use App\Repositories\Interfaces\BankRepositoryInterface;
use App\Services\Interfaces\BankServiceInterface;

class BankService implements BankServiceInterface
{
    protected $bankRepository;

    public function __construct(BankRepositoryInterface $bankRepository)
    {
        $this->bankRepository = $bankRepository;
    }

    public function createBankProfile(array $data)
    {
        return $this->bankRepository->createBankProfile($data);
    }
}
