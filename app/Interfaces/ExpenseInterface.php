<?php

namespace App\Interfaces;

interface ExpenseInterface
{
    public function addExpense();

    public function updateExpense();

    public function getExpenses();

    public function getExpense();

    public function getExpensesSummary();

    public function getOfficeExpenses();

    public function getMembersExpenses();

    public function getMemberExpenses();

    public function getTypeExpenses();

    public function uploadExpenses();

    public function deleteExpense();

    public function calculateYearlyTotalExpense();

    public function exportAllExpenses();

    public function exportMembersExpenses();

    public function exportOfficeExpenses();

    public function exportMemberExpenses();
}
