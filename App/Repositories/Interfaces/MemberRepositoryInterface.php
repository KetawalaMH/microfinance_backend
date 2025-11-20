<?php

namespace App\Repositories\Interfaces;


interface MemberRepositoryInterface
{
    public function create(array $data);
    public function getAllMembers();

    public function getMemberById($id);

    public function updateMember(array $data);

}