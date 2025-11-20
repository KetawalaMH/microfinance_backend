<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Log;
use Validator;

class TransactionController extends Controller
{
    protected function logError($url, $error_message)
    {
        Log::error('Error in setting controller function', [
            'url' => $url,
            'error' => $error_message
        ]);
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:saving_accounts,id',
            'amount' => 'required|numeric',
            'type' => 'required|in:credit,debit',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try {
            $transactionService = app()->make(\App\Services\Interfaces\TransactionServiceInterface::class);
            $transaction = $transactionService->createTransaction($request->all());

            return response()->json(['data' => $transaction], 201);
        } catch (Exception $e) {
            $url = $request->url();
            $error_message = $e->getMessage();
            $this->logError($url, $error_message);
            $output['success'] = false;
            $output['message'] = "Something went wrong, please try again: " . $e->getMessage();
            $output['data'] = null;

            return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:saving_accounts,id',
            'amount' => 'required|numeric',
            'type' => 'required|in:credit,debit',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try {
            $transactionService = app()->make(\App\Services\Interfaces\TransactionServiceInterface::class);
            $transaction = $transactionService->updateTransaction($id, $request->all());

            return response()->json(['data' => $transaction], 200);
        } catch (Exception $e) {
            $url = $request->url();
            $error_message = $e->getMessage();
            $this->logError($url, $error_message);
            $output['success'] = false;
            $output['message'] = "Something went wrong, please try again: " . $e->getMessage();
            $output['data'] = null;

            return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
        }
    }

    public function get(Request $request)
    {
        try {
            $transactionService = app()->make(\App\Services\Interfaces\TransactionServiceInterface::class);
            $transactions = $transactionService->getTransaction($request->all());

            return response()->json(['data' => $transactions], 200);
        } catch (Exception $e) {
            $url = $request->url();
            $error_message = $e->getMessage();
            $this->logError($url, $error_message);
            $output['success'] = false;
            $output['message'] = "Something went wrong, please try again: " . $e->getMessage();
            $output['data'] = null;

            return response()->json(['success' => $output['success'], 'message' => $output['message'], 'output' => $output['data']], 200);
        }
    }
}
