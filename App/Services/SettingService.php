<?php

namespace App\Services;

use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Services\Interfaces\LoanServiceInterface;
use App\Services\Interfaces\MemberServiceInterface;
use App\Services\Interfaces\SavingServiceInterface;
use App\Services\Interfaces\SettingServiceInterface;
use Exception;

class SettingService implements SettingServiceInterface
{
    private $settingRepository;

    public function __construct(SettingRepositoryInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function getMemberTypes()
    {
        return $this->settingRepository->getMemberTypes();
    }
    public function getAccountTypes()
    {
        return $this->settingRepository->getAccountTypes();
    }

    public function getDepartmntId($name)
    {
        return $this->settingRepository->getDepartmntId($name);
    }
    public function getBranchId($branch_code)
    {
        return $this->settingRepository->getBranchId($branch_code);
    }

    public function getLoanTypes()
    {
        return $this->settingRepository->getLoanTypes();
    }

    public function getDashboardData($data)
    {
        try {
            $loanService = app()->make(LoanServiceInterface::class);
            $totalLoans = $loanService->calculateLoanStats();
            if (!$totalLoans['success']) {
                return $totalLoans;
            }
            $loanData = $totalLoans['data'];

            $savingService = app()->make(SavingServiceInterface::class);
            $totalSaving = $savingService->calculateSavingStats();
            if (!$totalSaving['success']) {
                return $totalSaving;
            }
            $savingData = $totalSaving['data'];

            $memberService = app()->make(MemberServiceInterface::class);
            $totalMembers = $memberService->getMemberStats();
            if (!$totalMembers['success']) {
                return $totalMembers;
            }

            $accountData = $totalMembers['data'];
            return [
                'success' => true,
                'message' => 'Dashboard data fetched successfully.',
                'data' => [
                    'loanData' => $loanData,
                    'savingData' => $savingData,
                    'accountData' => $accountData,
                ]
            ];
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
