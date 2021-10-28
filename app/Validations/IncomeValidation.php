<?php

namespace App\Validations;

use App\Logic\IncomeLogic;

class IncomeValidation
{
    private $_incomeLogic;

    public function __construct(IncomeLogic $incomeLogic)
    {
        $this->_incomeLogic = $incomeLogic;
    }

    public function __validateIncome()
    {
        if (empty($this->_incomeLogic->month) || empty($this->_incomeLogic->staffId) || empty($this->_incomeLogic->year) || empty($this->_incomeLogic->amount) || empty($this->_incomeLogic->typeId)) {
            $response = [
                'success' => false,
                'message' => 'All fields are required'
            ];
            return ['response' => $response, 'code' => 400];
        }
        return true;
    }

    public function __validateIncomesUpload()
    {
        if (!$this->_incomeLogic->hasFile || empty($this->_incomeLogic->sheet) || empty($this->_incomeLogic->startRow)) {
            $response = [
                'success' => false,
                'message' => 'Make sure you select a file to upload'
            ];
            return ['response' => $response, 'code' => 400];
        }

        $extensions = ['xlsx', 'xls', 'csv'];
        if (!in_array($this->_incomeLogic->extension, $extensions)) {
            $response = [
                'success' => false,
                'message' => 'Make sure you upload an excel file of .xls, .xlsx or .csv extension'
            ];
            return ['response' => $response, 'code' => 400];
        }

        if ($this->_incomeLogic->size > 1048576) {
            $response = [
                'success' => false,
                'message' => 'Make sure the file does not exceed 1MB'
            ];
            return ['response' => $response, 'code' => 400];
        }

        return true;
    }
}
