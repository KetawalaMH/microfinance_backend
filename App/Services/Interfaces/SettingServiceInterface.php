<?php

namespace App\Services\Interfaces;

interface SettingServiceInterface
{
    public function getMemberTypes();
    public function getAccountTypes();

    public function getDepartmntId($name);
    public function getBranchId($branch_code);

    public function getLoanTypes();

    public function getDashboardData(array $data);
}
