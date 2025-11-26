<?php

namespace App\Repositories;

use App\Models\ActionLog;
use App\Models\Admin;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class AdminRepository implements AdminRepositoryInterface
{
    public function logAction(array $data)
    {
        try {
            $log = ActionLog::create($data);

            if (!$log) {
                return [
                    'success' => false,
                    'message' => 'Log not created',
                    'data' => null
                ];
            }
            $output['success'] = true;
            $output['message'] = "Member request approved successfully.";
            $output['data'] = $log;

            return $output;
        } catch (Exception $e) {
            $output['success'] = false;
            $output['message'] = "Something went wrong, please try again: " . $e->getMessage();
            $output['data'] = null;

            return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
        }
    }
}
