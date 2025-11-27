<?php

namespace App\Repositories\Interfaces;

interface TransactionRepositoryInterface
{
    public function createTransaction(array $data);
    public function updateTransaction($id, array $data);
    public function getTransaction(array $data);

    public function addInstallments(array $data);

    public function getpaymentData($loanId);
}
