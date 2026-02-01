<?php

namespace App\Services;

use App\Models\SavingAccount;
use App\Repositories\Interfaces\SavingAccountRepositoryInterface;
use App\Repositories\Interfaces\SavingRepositoryInterface;
use App\Services\Interfaces\MemberServiceInterface;
use App\Services\Interfaces\SavingServiceInterface;
use App\Services\Interfaces\TransactionServiceInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log as FacadesLog;
use Log;

class SavingService implements SavingServiceInterface
{
    protected $repository;

    protected $memberService;


    public function __construct(SavingRepositoryInterface $repository, MemberServiceInterface $memberService)
    {
        $this->repository = $repository;
        $this->memberService = $memberService;
    }

    public function createSavingAccount(array $data)
    {
        try {
            $member = $this->memberService->getMemberById($data['member_id']);
            if (!$member['success']) {
                return $member;
            }
            if ($member['data']->status != 'active') {
                return [
                    'success' => false,
                    'message' => 'Member is not active',
                    'data' => null
                ];
            }
            return $this->repository->createSavingAccount($data);
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
        return $this->repository->updateSavingAccount($data);
    }

    public function getSavingAccounts(array $data)
    {
        try {
            $savingAcconts = $this->repository->getSavingAccounts($data);
            if ($savingAcconts['success'] === false) {
                return $savingAcconts;
            }
            $savingAcconts = $savingAcconts['data'];
            $totalSaving = 0;
            $totalInterestRate = 0;
            $count = 0;
            $formatedSavingAccounts = $savingAcconts->map(function ($savingAccount) use (&$totalSaving, &$totalInterestRate, &$count) {
                $totalSaving += $savingAccount->current_balance;
                $totalInterestRate += $savingAccount->accountType->interest_rate;
                $count++;
                return [
                    'id' => $savingAccount->id,
                    'account_number' => $savingAccount->account_number,
                    'account_holder' => $savingAccount->member->full_name,
                    'current_balance' => $savingAccount->current_balance,
                    'account_type' => $savingAccount->accountType->type_name,
                    'interest_rate' => $savingAccount->accountType->interest_rate,
                    'status' => $savingAccount->status,
                    'created_at' => $savingAccount->created_at,
                ];
            });
            $averageInterestRate = $count > 0 ? $totalInterestRate / $count : 0;
            return [
                'success' => true,
                'message' => 'Saving accounts fetched successfully',
                'data' => ['saving_accounts' => $formatedSavingAccounts, 'total_saving' => $totalSaving, 'avg_interest_rate' => $averageInterestRate]
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
        return $this->repository->getSavingAccountById($id);
    }

    public function getSavingAccountDetails($id)
    {
        try {
            $savingAccount = $this->repository->getSavingAccountById($id);
            if (!$savingAccount['success']) {
                return $savingAccount;
            }

            FacadesLog::info($savingAccount);
            $savingAccount = $savingAccount['data'];

            $accountInfomation = [
                'account_holder' => $savingAccount->member->full_name,
                'email_address' => $savingAccount->member->email_address,
                'current_balance' => $savingAccount->current_balance,
                'interest_rate' => $savingAccount->accountType->interest_rate,
                'savingAccount_catogery' => $savingAccount->accountType->savingAccount_type,
                'account_opened' => $savingAccount->created_at,
                'status' => $savingAccount->status,
            ];

            $transactionService = app(TransactionServiceInterface::class);

            $transactions = $transactionService->getTransactionHistory($id);

            $transactionHistory = [];

            if ($transactions->isNotEmpty()) {
                foreach ($transactions as $transaction) {
                    $transactionHistory[] = [
                        'amount' => $transaction->amount,
                        'title' => $transaction->title,
                        'balance' => $transaction->balance,
                        'date' => $transaction->created_at,
                        'type' => $transaction->type,
                    ];
                }
            }

            $accountSummary = $this->prepareAccountSummary($transactionHistory);
            return [
                'success' => true,
                'message' => 'Saving account details fetched successfully',
                'data' => [
                    'account_information' => $accountInfomation,
                    'account_summary' => $accountSummary,
                    'transaction_history' => $transactionHistory
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function calculateSavingStats()
    {
        $response = $this->repository->getTotalSavingData();

        if (!$response || $response->count() === 0) {
            return [
                'success' => true,
                'message' => 'Saving data not found.',
                'data' => [
                    'total_amount' => 0,
                    'current_month_total' => 0,
                    'last_month_total' => 0,
                    'growth_rate' => round(0, 2),
                ]
            ];
        }

        $savings = $response;

        // 1. Total saving amount
        $totalAmount = $savings->sum('current_balance');

        // 2. Monthly calculations
        $currentMonth = now()->month;
        $lastMonth = now()->subMonth()->month;

        $currentMonthTotal = $savings->filter(function ($saving) use ($currentMonth) {
            return $saving->created_at &&
                Carbon::parse($saving->created_at)->month == $currentMonth;
        })->sum('current_balance');

        $lastMonthTotal = $savings->filter(function ($saving) use ($lastMonth) {
            return $saving->created_at &&
                Carbon::parse($saving->created_at)->month == $lastMonth;
        })->sum('current_balance');

        // 3. Growth rate
        if ($lastMonthTotal == 0) {
            $growthRate = 100; // or 0
        } else {
            $growthRate = (($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100;
        }

        return [
            'success' => true,
            'message' => 'Saving statistics calculated successfully.',
            'data' => [
                'total_amount' => $totalAmount,
                'current_month_total' => $currentMonthTotal,
                'last_month_total' => $lastMonthTotal,
                'growth_rate' => round($growthRate, 2),
            ]
        ];
    }

    private function prepareAccountSummary(array $transactionHistory)
    {
        // If empty → return zero summary
        if (empty($transactionHistory)) {
            return [
                'total_deposit'   => 0,
                'total_withdraw'  => 0,
                'total_interest'  => 0,
                'this_month'      => 0,
            ];
        }

        $totalDeposit  = 0;
        $totalWithdraw = 0;
        $totalInterest = 0;
        $thisMonth     = 0;

        $currentMonth = now()->format('Y-m');

        foreach ($transactionHistory as $txn) {

            // Calculate deposit
            if (isset($txn['type']) && $txn['type'] === 'deposit') {
                $totalDeposit += $txn['amount'];
            }

            // Calculate withdraw
            if (isset($txn['type']) && $txn['type'] === 'withdraw') {
                $totalWithdraw += $txn['amount'];
            }

            // Calculate interest
            if (isset($txn['type']) && $txn['type'] === 'interest') {
                $totalInterest += $txn['amount'];
            }

            // Calculate this month totals
            if (isset($txn['date']) && \Carbon\Carbon::parse($txn['date'])->format('Y-m') == $currentMonth) {
                $thisMonth += $txn['amount'];
            }
        }

        return [
            'total_deposit'   => $totalDeposit,
            'total_withdraw'  => $totalWithdraw,
            'total_interest'  => $totalInterest,
            'this_month'      => $thisMonth,
        ];
    }

    public function approveSavingAccount($data)
    {
        try {
            $response = $this->repository->updateSavingStatus($data['account_id'], 'active');
            if (!$response['success']) {
                return $response;
            }

            return [
                'success' => true,
                'message' => 'Saving account approved successfully.',
                'data' => $response['data']
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }
    public function rejectSavingAccount($data)
    {
        try {
            $response = $this->repository->updateSavingStatus($data['account_id'], 'inactive');
            if (!$response['success']) {
                return $response;
            }

            return [
                'success' => true,
                'message' => 'Saving account rejected successfully.',
                'data' => $response['data']
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }
}
