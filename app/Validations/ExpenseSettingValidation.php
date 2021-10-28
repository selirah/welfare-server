<?php

namespace App\Validations;

use App\Logic\ExpenseSettingLogic;

class ExpenseSettingValidation
{
    private $_expenseSettingLogic;

    public function __construct(ExpenseSettingLogic $expenseSettingLogic)
    {
        $this->_expenseSettingLogic = $expenseSettingLogic;
    }

    public function __validateExpenseSetting()
    {
        if (empty($this->_expenseSettingLogic->type) || empty($this->_expenseSettingLogic->parentId)) {
            $response = [
                'success' => false,
                'message' => 'All fields are required'
            ];
            return ['response' => $response, 'code' => 400];
        }

        return true;
    }
}
