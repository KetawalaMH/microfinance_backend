<?php

namespace App\Services\Interfaces;

interface MemberServiceInterface
{
    public function createMember(array $data);
    public function getALlMembers();
}
