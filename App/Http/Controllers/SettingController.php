<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\SettingServiceInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // Import Log facade at the top

class SettingController extends Controller
{
    private $settingService;

    public function __construct(SettingServiceInterface $settingService)
    {
        $this->settingService = $settingService;
    }

    protected function logError($url, $error_message)
    {
        Log::error('Error in setting controller function', [
            'url' => $url,
            'error' => $error_message
        ]);
    }

    public function getMemberTypes()
    {
        try {
            $memberTypes = $this->settingService->getMemberTypes();

            return response()->json([
                'success' => true,
                'data' => $memberTypes
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching member types.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAccountTypes()
    {
        try {
            $accountTypes = $this->settingService->getAccountTypes();

            return response()->json(
                $accountTypes,
                200
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching account types.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLoanTypes()
    {
        try {
            $loanTypes = $this->settingService->getLoanTypes();

            return response()->json($loanTypes, 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching loan types.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDashboardData(Request $request)
    {
        try {
            $dashboardData = $this->settingService->getDashboardData($request->all());

            return response()->json($dashboardData, 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching dashboard data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
