<?php

namespace App\Interfaces;

interface IncomeInterface
{
    public function addIncome();

    public function updateIncome();

    public function getIncomes();

    public function getIncome();

    public function getIncomesSummary();

    public function getOfficeIncomes();

    public function getMembersIncomes();

    public function getMemberIncomes();

    public function getTypeIncomes();

    public function uploadIncomes();

    public function deleteIncome();

    public function calculateYearlyTotalIncome();

    public function exportAllIncomes();

    public function exportMembersIncomes();

    public function exportOfficeIncomes();

    public function exportMemberIncomes();
}
