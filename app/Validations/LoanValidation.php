<?php

namespace App\Validations;


use App\Logic\LoanLogic;

class LoanValidation
{
    private $_loanLogic;

    public function __construct(LoanLogic $loanLogic)
    {
        $this->_loanLogic = $loanLogic;
    }

    public function __validateLoan()
    {
        if (
            empty($this->_loanLogic->amountLoaned) || empty($this->_loanLogic->staffId) ||
            empty($this->_loanLogic->password) || empty($this->_loanLogic->loanType) ||
            empty($this->_loanLogic->time)
        ) {
            $response = [
                'success' => false,
                'message' => 'All fields are required'
            ];
            return ['response' => $response, 'code' => 400];
        }

        return true;
    }

    public function __validateUpdate()
    {
        if (empty($this->_loanLogic->amountPaid) || empty($this->_loanLogic->password)) {
            $response = [
                'success' => false,
                'message' => 'All fields are required'
            ];
            return ['response' => $response, 'code' => 400];
        }
        return true;
    }
}
