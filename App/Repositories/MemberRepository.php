<?php

namespace App\Repositories;

use App\Models\Member;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

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


    public function getAllMembers()
    {
        try {
            $members = Member::orderBy('id', 'desc')->get();

            if ($members->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No members found.',
                    'data' => null,
                ];
            }

            $formatedMembers = $members->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'contact' => $member->mobile_number,
                    'createdAt' => $member->created_at->format('Y-m-d'),
                ];
            });

            $total_members = $members->count();
            $active_members = $members->where('status', 'active')->count();
            $inactive_members = $members->where('status', 'inactive')->count();
            $pending_members = $members->where('status', 'pending')->count();

            return [
                'success' => true,
                'message' => 'Members fetched successfully.',
                'data' => [
                    'members' => $formatedMembers,
                    'total_members' => $total_members,
                    'active_members' => $active_members,
                    'inactive_members' => $inactive_members,
                    'pending_members' => $pending_members,
                ],
            ];
        } catch (Exception $e) {
            $url = 'member/get-all-members';
            $this->logError($url, $e->getMessage());

            return [
                'success' => false,
                'message' => 'Something went wrong, please try again later.',
                'data' => null,
            ];
        }
    }

    public function getMemberById($id)
    {
        try {
            $member = Member::find($id);

            if (!$member) {
                return [
                    'success' => false,
                    'message' => 'Member not found.',
                    'data' => null,
                ];
            }

            return [
                'success' => true,
                'message' => 'Member fetched successfully.',
                'data' => $member,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function updateMember(array $data)
    {
        try {
            $member = Member::find($data['id']);
            $member->update($data);
            return [
                'success' => true,
                'message' => 'Member updated successfully.',
                'data' => $member,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }
}
