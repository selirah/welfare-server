<?php

namespace App\Validations;

use App\Logic\IncomeSettingLogic;

class IncomeSettingValidation
{
    private $_incomeSettingLogic;

    public function __construct(IncomeSettingLogic $incomeSettingLogic)
    {
        $this->_incomeSettingLogic = $incomeSettingLogic;
    }

    public function __validateIncomeSetting()
    {
        if (empty($this->_incomeSettingLogic->type) || empty($this->_incomeSettingLogic->parentId)) {
            $response = [
                'success' => false,
                'message' => 'All fields are required'
            ];
            return ['response' => $response, 'code' => 400];
        }

        return true;
    }
}
