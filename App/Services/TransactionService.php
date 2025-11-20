<?php

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Services\Interfaces\SavingServiceInterface;
use Log;

class TransactionService
{
    protected $transactionRepository;
    protected $savingService;

    public function __construct(TransactionRepositoryInterface $transactionRepository, SavingServiceInterface $savingService)
    {
        $this->transactionRepository = $transactionRepository;
        $this->savingService = $savingService;
    }

    protected function logError($url, $error_message)
    {
        Log::error('Error in setting controller function', [
            'url' => $url,
            'error' => $error_message
        ]);
    }

    public function createTransaction(array $data)
    {
        try {
            $account = $this->savingService->getSavingAccounts(['id' => $data['account_id']]);
            if ($account['success'] === false) {
                return $account;
            }
            $currrentBalance = $account['data'][0]->balance;
            $updatedBalance = $account['data'][0]->balance;
            if ($data['type'] === 'deposite') {
                $updatedBalance += $data['amount'];
            } else if ($data['type'] === 'withdraw') {
                $updatedBalance -= $data['amount'];
            } else if ($data['type'] === 'interest') {
                $updatedBalance += $data['amount'];
            }
            $data['account_id'] = $account['data'][0]->id;
            $data['balance'] = $updatedBalance;
            $response = $this->transactionRepository->createTransaction($data);
            if (!$response['success']) {
                return $response;
            }
            $savingAccout = $this->savingService->updateSavingAccount(['id' => $account['data'][0]->id, 'current_balance' => $updatedBalance]);
            if (!$savingAccout['success']) {
                $this->transactionRepository->updateTransaction($response['data']->id, ['is_active' => 'false']);
                return $savingAccout;
            }
            return $response;
        } catch (\Exception $e) {
            $url = "transactions";
            $this->logError($url, $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Something went wrong, please try again: " . $e->getMessage()
            ], 500);
        }
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
