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

    public function updateSavingAccount(array $data)
    {
        try {
            SavingAccount::where('id', $data['id'])->update($data);

            $savingAccount = SavingAccount::find($data['id']);
            return [
                'success' => true,
                'message' => 'Saving account updated successfully',
                'data' => $savingAccount
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function getSavingAccounts(array $data)
    {
        try {
            $query = SavingAccount::with(['accountType', 'member']); // <-- add eager loading

            if (isset($data['account_type_id'])) {
                $query->where('account_type_id', $data['account_type_id']);
            }

            if (isset($data['status'])) {
                $query->where('status', $data['status']);
            }

            $savingAccounts = $query->get();

            return [
                'success' => true,
                'message' => 'Saving accounts fetched successfully',
                'data' => $savingAccounts
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function getSavingAccountById($id)
    {
        try {
            $savingAccount = SavingAccount::find($id);
            if (!$savingAccount) {
                return [
                    'success' => false,
                    'message' => 'Saving account not found',
                    'data' => null
                ];
            }
            return [
                'success' => true,
                'message' => 'Saving account fetched successfully',
                'data' => $savingAccount
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
