<?php

namespace App\Repositories\Interfaces;

interface LoanRepositoryInterface
{
    public function getLoanTypeById($id);

    public function createLoanRequest(array $data);

    public function saveLoanDocuments(array $data);

    public function addGaurantors(array $data);

    public function getMemberDueLoans($memberId);

    public function submitLoanApplication(array $data);

    public function getLoanById($id);

    public function getAllLoanApplications(array $data);

    public function getTotalLoanData();

    public function getLoanTypeDetails();

    public function updateLoanStatus($id, $status);
}
