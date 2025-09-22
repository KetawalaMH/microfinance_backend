<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // Import Log facade at the top

class SettingController extends Controller
{
    private $settingRepository;

    public function __construct(SettingRepositoryInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    protected function logError($url, $error_message)
    {
        Log::error('Error in setting controller function', [
            'url' => $url,
            'error' => $error_message
        ]);
    }  
}