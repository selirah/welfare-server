<?php

namespace App\Validations;


use App\Logic\DonationLogic;

class DonationValidation
{
    private $_donationLogic;

    public function __construct(DonationLogic $donationLogic)
    {
        $this->_donationLogic = $donationLogic;
    }

    public function __validateDonation()
    {
        if (empty($this->_donationLogic->month) || empty($this->_donationLogic->staffId) || empty($this->_donationLogic->year) || empty($this->_donationLogic->amount) || empty($this->_donationLogic->typeId)) {
            $response = [
                'success' => false,
                'message' => 'All fields are required'
            ];
            return ['response' => $response, 'code' => 400];
        }
        return true;
    }

    public function __validateDonationsUpload()
    {
        if (!$this->_donationLogic->hasFile || empty($this->_donationLogic->sheet) || empty($this->_donationLogic->startRow)) {
            $response = [
                'success' => false,
                'message' => 'Make sure you select a file to upload'
            ];
            return ['response' => $response, 'code' => 400];
        }

        $extensions = ['xlsx', 'xls', 'csv'];
        if (!in_array($this->_donationLogic->extension, $extensions)) {
            $response = [
                'success' => false,
                'message' => 'Make sure you upload an excel file of .xls, .xlsx or .csv extension'
            ];
            return ['response' => $response, 'code' => 400];
        }

        if ($this->_donationLogic->size > 1048576) {
            $response = [
                'success' => false,
                'message' => 'Make sure the file does not exceed 1MB'
            ];
            return ['response' => $response, 'code' => 400];
        }

        return true;
    }
}
