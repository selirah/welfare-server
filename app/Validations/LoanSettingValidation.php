<?php

namespace App\Validations;


use App\Logic\LoanSettingLogic;

class LoanSettingValidation
{
    private $_loanSettingLogic;

    public function __construct(LoanSettingLogic $loanSettingLogic)
    {
        $this->_loanSettingLogic = $loanSettingLogic;
    }

    public function __validateLoanSetting()
    {
        if (empty($this->_loanSettingLogic->type) || empty($this->_loanSettingLogic->rate) || empty($this->_loanSettingLogic->minMonth) || empty($this->_loanSettingLogic->maxMonth)) {
            $response = [
                'success' => false,
                'message' => 'All fields are required'
            ];
            return ['response' => $response, 'code' => 400];
        }

        if ($this->_loanSettingLogic->minMonth > $this->_loanSettingLogic->maxMonth) {
            $response = [
                'success' => false,
                'message' => 'The minimum number of month must be less than the maximum number of months'
            ];
            return ['response' => $response, 'code' => 400];
        }

        return true;
    }
}
