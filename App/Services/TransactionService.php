<?php

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\Interfaces\TransactionRepositoryInterface;

class TransactionService
{
    protected $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function createTransaction(array $data)
    {
        return $this->transactionRepository->createTransaction($data);
    }

    public function updateTransaction($id, array $data)
    {
        return $this->transactionRepository->updateTransaction($id, $data);
    }

    public function getTransaction(array $data)
    {
        return $this->transactionRepository->getTransaction($data);
    }
}
