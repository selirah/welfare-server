<?php

namespace App\Validations;


use App\Logic\ContributionLogic;

class ContributionValidation
{
    private $_contributionLogic;

    public function __construct(ContributionLogic $contributionLogic)
    {
        $this->_contributionLogic = $contributionLogic;
    }

    public function __validateContribution()
    {
        if (empty($this->_contributionLogic->month) || empty($this->_contributionLogic->year)) {
            $response = [
                'success' => false,
                'message' => 'Month and year fields are all required'
            ];
            return ['response' => $response, 'code' => 400];
        }

        if (($this->_contributionLogic->paymentType == env('NON_UNIFORM')) && (empty($this->_contributionLogic->amount) || empty($this->_contributionLogic->staffId))) {
            $response = [
                'success' => false,
                'message' => 'The staff ID and the amount fields are required'
            ];
            return ['response' => $response, 'code' => 400];
        }

        return true;
    }

    public function __validateContributionsUpload()
    {
        if (!$this->_contributionLogic->hasFile || empty($this->_contributionLogic->sheet) || empty($this->_contributionLogic->startRow)) {
            $response = [
                'success' => false,
                'message' => 'Make sure you select a file to upload'
            ];
            return ['response' => $response, 'code' => 400];
        }

        $extensions = ['xlsx', 'xls', 'csv'];
        if (!in_array($this->_contributionLogic->extension, $extensions)) {
            $response = [
                'success' => false,
                'message' => 'Make sure you upload an excel file of .xls, .xlsx or .csv extension'
            ];
            return ['response' => $response, 'code' => 400];
        }

        if ($this->_contributionLogic->size > 1048576) {
            $response = [
                'success' => false,
                'message' => 'Make sure the file does not exceed 1MB'
            ];
            return ['response' => $response, 'code' => 400];
        }

        return true;
    }
}
