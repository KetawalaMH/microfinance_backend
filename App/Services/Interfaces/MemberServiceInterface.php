<?php

namespace App\Services\Interfaces;

interface MemberServiceInterface
{
    public function createMember(array $data);
    public function getALlMembers();

    public function getMemberById($id);

    public function updateMember(array $data);

    public function getMemberDetails($id);
}
