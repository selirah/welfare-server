<?php

namespace App\Interfaces;

interface ExpenseSettingInterface
{
    public function addExpenseSetting();

    public function updateExpenseSetting();

    public function deleteExpenseSetting();

    public function getExpenseSettings();

    public function getExpenseSetting();

    public function getExpenseSettingsWithParentId();
}
