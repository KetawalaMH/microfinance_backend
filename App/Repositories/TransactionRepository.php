<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository implements Interfaces\TransactionRepositoryInterface
{
    public function createTransaction(array $data)
    {
        try {
            $transaction = Transaction::create($data);
            if (!$transaction) {
                return [
                    'success' => false,
                    'message' => 'Transaction not created',
                    'data' => null
                ];
            }
            return [
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => $transaction
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
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
