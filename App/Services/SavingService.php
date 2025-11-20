<?php

namespace App\Services;

use App\Models\SavingAccount;
use App\Repositories\Interfaces\SavingAccountRepositoryInterface;
use App\Repositories\Interfaces\SavingRepositoryInterface;
use App\Services\Interfaces\MemberServiceInterface;
use App\Services\Interfaces\SavingServiceInterface;
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
}
