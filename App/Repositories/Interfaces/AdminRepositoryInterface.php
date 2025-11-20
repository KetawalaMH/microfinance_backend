<?php

namespace App\Repositories\Interfaces;

use App\Models\Admin;

interface AdminRepositoryInterface
{
    public function approveMemberRequest(array $data);
}
