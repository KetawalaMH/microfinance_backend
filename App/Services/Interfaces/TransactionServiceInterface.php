<?php

namespace App\Services\Interfaces;

use App\Repositories\Interfaces\TransactionRepositoryInterface;

interface TransactionServiceInterface
{
    public function createTransaction(array $data);
    public function updateTransaction($id, array $data);
    public function getTransaction(array $data);

    public function addInstallments(array $data);

    public function getpaymentData($loanId);

    public function getTransactionHistory($id);
}
