<?php

namespace App\Services;

use App\Services\Interfaces\HuggingfaceModelServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HuggingfaceModelService implements HuggingfaceModelServiceInterface
{
    protected string $apiUrl;
    protected string $apiKey;

    public function __construct()
    {
        $modelId = "pasindu-1999/loan_prediction_tinyllama";

        $this->apiUrl = "https://router.huggingface.co/hf-inference/models/{$modelId}";
        $this->apiKey = config('services.huggingface.api_key');
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

        $response = Http::withOptions(['verify' => false])->withToken($this->apiKey)
            ->timeout(120)
            ->retry(2, 1000)
            ->post($this->apiUrl, [
                'inputs' => $prompt,
                'parameters' => [
                    'max_new_tokens' => 300,
                    'temperature' => 0.3,
                ],
            ]);

        Log::info('HF response status', ['status' => $response->status()]);
        Log::info('HF response body', ['body' => $response->body()]);

        if ($response->failed()) {
            return [
                'error' => true,
                'status' => $response->status(),
                'message' => $response->body(),
            ];
        }

        return $response->json();
    }
}
