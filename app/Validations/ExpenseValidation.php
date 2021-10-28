<?php

namespace App\Validations;

use App\Logic\ExpenseLogic;

class ExpenseValidation
{
    private $_expenseLogic;

    public function __construct(ExpenseLogic $expenseLogic)
    {
        $this->_expenseLogic = $expenseLogic;
    }

    public function __validateExpense()
    {
        if (empty($this->_expenseLogic->month) || empty($this->_expenseLogic->staffId) || empty($this->_expenseLogic->year) || empty($this->_expenseLogic->amount) || empty($this->_expenseLogic->typeId)) {
            $response = [
                'success' => false,
                'message' => 'All fields are required'
            ];
            return ['response' => $response, 'code' => 400];
        }
        return true;
    }

    public function __validateExpensesUpload()
    {
        if (!$this->_expenseLogic->hasFile || empty($this->_expenseLogic->sheet) || empty($this->_expenseLogic->startRow)) {
            $response = [
                'success' => false,
                'message' => 'Make sure you select a file to upload'
            ];
            return ['response' => $response, 'code' => 400];
        }

        $extensions = ['xlsx', 'xls', 'csv'];
        if (!in_array($this->_expenseLogic->extension, $extensions)) {
            $response = [
                'success' => false,
                'message' => 'Make sure you upload an excel file of .xls, .xlsx or .csv extension'
            ];
            return ['response' => $response, 'code' => 400];
        }

        if ($this->_expenseLogic->size > 1048576) {
            $response = [
                'success' => false,
                'message' => 'Make sure the file does not exceed 1MB'
            ];
            return ['response' => $response, 'code' => 400];
        }

        return true;
    }
}
