<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface SettingRepositoryInterface
{
    public function getMemberTypes();
    public function getAccountTypes();

    public function getDepartmntId($name);
    public function getBranchId($name);

}