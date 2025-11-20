<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\TransactionServiceInterface;
use Exception;
use Illuminate\Http\Request;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class TransactionController extends Controller
{
    private $transactionService;

    public function __construct(TransactionServiceInterface $transactionService)
    {
        $this->transactionService = $transactionService;
    }
    protected function logError($url, $error_message)
    {
        Log::error('Error in setting controller function', [
            'url' => $url,
            'error' => $error_message
        ]);
    }
    public function createTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:saving_accounts,id',
            'amount' => 'required|numeric',
            'type' => 'required|in:deposite,withdraw,interest',
            'description' => 'nullable|string',
            'title' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try {

            $data = $request->all();
            $data['recorded_by'] = JWTAuth::user()->id;

            $transaction = $this->transactionService->createTransaction($data);

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
