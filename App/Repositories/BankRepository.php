<?php

namespace App\Repositories;

use App\Models\BankProfile;
use App\Repositories\Interfaces\BankRepositoryInterface;

use Illuminate\Support\Facades\Log;

class BankRepository implements BankRepositoryInterface
{
     protected function logError($url, $error_message)
    {
        Log::error('Error in user repository function', [
            'url' => $url,
            'error' => $error_message
        ]);
    }
    public function createBankProfile(array $data)
    {
        try{
            $bank_name = isset($data['bank_name']) ? $data['bank_name'] : null;
            $location = isset($data['location']) ? $data['location'] : null;
            $owner_id = isset($data['owner_id']) ? $data['owner_id'] : null;

            $is_owner_id_exist_ = BankProfile::where('owner_id', $owner_id)
                            ->where('is_active', 1)
                            ->orderBy('id', 'desc')->first();

            if ($is_owner_id_exist_) {
                $output['success'] = false;
                $output['message'] = "The owner id you've entered is already associated with an existing bank profile!";
                $output['data'] = null;
            } else {
                $date_time = date('Y-m-d H:i:s');

                $new_bank_profile = BankProfile::create([
                                'bank_name' => $bank_name,
                                'location' => $location,
                                'owner_id' => $owner_id,
                                'is_active' => 1,
                                'created_at' => $date_time,
                                'updated_at' => $date_time
                            ]);

                $output['success'] = true;
                $output['message'] = "Bank profile created successfully.";
                $output['data'] = $new_bank_profile;
            }
        } catch (\Exception $e) {
            $url = "bank/create";
            $error_message = $e->getMessage();
            $this->logError($url, $error_message);
            $output['success'] = false;
            $output['message'] = "Something went wrong, please try again: " . $e->getMessage();
            $output['data'] = null;
        }

        return $output;
    }

}