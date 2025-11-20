<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository implements Interfaces\TransactionRepositoryInterface
{
    public function createTransaction(array $data)
    {
        return Transaction::create($data);
    }

    public function updateTransaction($id, array $data)
    {
        return Transaction::where('id', $id)->update($data);
    }

    public function getTransaction(array $data)
    {
        return Transaction::where($data)->get();
    }
}
