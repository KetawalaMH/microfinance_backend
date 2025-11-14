<?php

namespace App\Repositories;

use App\Models\SavingAccount;
use App\Repositories\Interfaces\SavingRepositoryInterface;

class SavingRepository implements SavingRepositoryInterface
{
    public function createSavingAccount(array $data)
    {
        try {
            $account = SavingAccount::create($data);

            return [
                'success' => true,
                'message' => 'Saving account created successfully',
                'data' => $account
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }
}
