<?php

namespace App\Repositories;

use App\Models\Member;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use Exception;
use Log;

class MemberRepository implements MemberRepositoryInterface
{
    protected function logError($url, $error_message)
    {
        Log::error('Error in user repository function', [
            'url' => $url,
            'error' => $error_message
        ]);
    }

    public function create(array $data)
    {
        try {
            $member = Member::create($data);
            $output['success'] = true;
            $output['message'] = "Member created successfully.";
            $output['data'] = $member;

        } catch (Exception $e) {
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
