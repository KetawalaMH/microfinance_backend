<?php

namespace App\Services;

use App\Services\Interfaces\HuggingfaceModelServiceInterface;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HuggingfaceModelService implements HuggingfaceModelServiceInterface
{
    protected string $apiUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiUrl = "http://34.170.190.201/predict";
    }

    private function buildPrompt(array $data): string
    {
        return <<<PROMPT
[INST]
Analyze this loan application:

Customer ID: {$data['Customer_National_ID']}
Age: {$data['Age']}
Employment: {$data['Employment']}
Income: {$data['Income']}
Employment Years: {$data['Employment_Years']}
Business Type: {$data['Business_Type']}
Existing Debts: {$data['Existing_Debts']}

Previous Loan History: {$data['Previous_Loan_History']}
Previous Defaults: {$data['Previous_Loan_Defaults']}
Reason for Defaults: {$data['Reason_for_Loan_Defaults']}

Loan Purpose: {$data['Loan_Purpose']}
Assets: {$data['Assets']}

Guarantor ID: {$data['Guarantee_National_ID']}
Guarantor Employment: {$data['Guarantee_Employment']}
Guarantor Income: {$data['Guarantee_Income']}

Give predictions for:
- Loan Eligibility
- Repayment Capacity
- Fraud Risk
- Optimal Tenure
- Recommended Loan Amount
[/INST]
PROMPT;
    }

    public function predict(array $data): array
    {
        $prompt = $this->buildPrompt($data);

        $response = Http::withOptions(['verify' => false])
            ->timeout(120)
            ->retry(2, 1000)
            ->post($this->apiUrl, [
                'prompt' => $prompt,
            ]);

        $hfResponse = json_decode($response, true); // $responseBody = HF raw response
        Log::info($hfResponse);

        // // Decode the inner "body"
        // $innerBody = json_decode($hfResponse['body'], true);

        // This is the actual text from the model
        $text = $hfResponse['response'];

        $data = [];

        preg_match('/Loan Eligibility:\s*(.*)/', $text, $m);
        $data['loan_eligibility'] = $m[1] ?? null;

        preg_match('/Repayment Capacity:\s*(.*)/', $text, $m);
        $data['repayment_capacity'] = $m[1] ?? null;

        preg_match('/Fraud Risk:\s*(.*)/', $text, $m);
        $data['fraud_risk'] = $m[1] ?? null;

        preg_match('/Optimal Tenure:\s*(.*)/', $text, $m);
        $data['optimal_tenure'] = $m[1] ?? null;

        preg_match('/Recommended Loan Amount:\s*(.*)/', $text, $m);
        $data['recommended_loan_amount'] = $m[1] ?? null;


        if ($response->failed()) {
            return [
                'error' => true,
                'status' => $response->status(),
                'message' => $response->body(),
            ];
        }

        return [
            'success' => true,
            'message' => 'Prediction suceeded',
            'data' => $data,
        ];
    }
}
