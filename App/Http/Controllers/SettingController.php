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
}