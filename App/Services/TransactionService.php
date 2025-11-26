<?php

namespace App\Services;

use App\Models\SavingAccount;
use App\Models\Transaction;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Services\Interfaces\SavingServiceInterface;
use App\Services\Interfaces\TransactionServiceInterface;
use Illuminate\Support\Facades\Log;

class TransactionService implements TransactionServiceInterface
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
            // Fetch account
            $result = $this->savingService->getSavingAccountById($data['account_id']);

            if ($result['success'] === false) {
                return $result;
            }

            /** @var SavingAccount $account */
            $account = $result['data']; // This is a model object

            // Current balance
            $updatedBalance = $account->current_balance;

            // Apply transaction
            if ($data['type'] === 'deposite') {
                $updatedBalance += $data['amount'];
            } else if ($data['type'] === 'withdraw') {
                $updatedBalance -= $data['amount'];
            } else if ($data['type'] === 'interest') {
                $updatedBalance += $data['amount'];
            }

            // Prepare data for creating transaction
            $data['account_id'] = $account->id;
            $data['balance'] = $updatedBalance;
            $data['confirmed_by'] = null;

            // Create transaction
            $response = $this->transactionRepository->createTransaction($data);

            if (!$response['success']) {
                Log::error('Transaction failed');
                return $response;
            }

            Log::info('Transaction created');

            // Now update the saving account balance
            $savingUpdate = $this->savingService->updateSavingAccount([
                'id' => $account->id,
                'current_balance' => $updatedBalance
            ]);

            if (!$savingUpdate['success']) {
                // Rollback - deactivate transaction
                $this->transactionRepository->updateTransaction($response['data']->id, ['is_active' => false]);
                return $savingUpdate;
            }

            return $response;
        } catch (\Exception $e) {
            $this->logError("transactions", $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Something went wrong: " . $e->getMessage()
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

    public function addInstallments($data)
    {
        return $this->transactionRepository->addInstallments($data);
    }

    public function getpaymentData($loanId)
    {
        return $this->transactionRepository->getpaymentData($loanId);
    }
}
