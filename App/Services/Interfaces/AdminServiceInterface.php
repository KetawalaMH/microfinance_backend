<?php

namespace App\Services\Interfaces;

use App\Repositories\Interfaces\AdminRepositoryInterface;

interface AdminServiceInterface
{
    public function approveMemberRquest(array $data);

    public function approveLoanRequest(array $data);

    public function logAction(array $data);

    public function rejectLoanRequest(array $data);
}
