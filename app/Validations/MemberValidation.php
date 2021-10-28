<?php

namespace App\Validations;


use App\Logic\MemberLogic;

class MemberValidation
{
    private $_memberLogic;

    public function __construct(MemberLogic $memberLogic)
    {
        $this->_memberLogic = $memberLogic;
    }

    public function __validateMember()
    {
        if (empty($this->_memberLogic->staffId) || empty($this->_memberLogic->memberName)) {
            $response = [
                'message' => 'Name and ID fields are all required'
            ];
            return ['response' => $response, 'code' => 400];
        }

        if (!filter_var($this->_memberLogic->memberEmail, FILTER_VALIDATE_EMAIL)) {
            $response = [
                'message' => 'Provide a valid email address in the format john@doe.com'
            ];
            return ['response' => $response, 'code' => 400];
        }

        if (($this->_memberLogic->paymentType == env('FIXED_RATE')) && empty($this->_memberLogic->amount)) {
            $response = [
                'message' => 'Please provide a fixed amount of payment for this member'
            ];
            return ['response' => $response, 'code' => 400];
        }

        return true;
    }

    public function __validateMembersUpload()
    {
        if (!$this->_memberLogic->hasFile || empty($this->_memberLogic->sheet) || empty($this->_memberLogic->startRow)) {
            $response = [
                'message' => 'Make sure you select a file, sheet number and start row'
            ];
            return ['response' => $response, 'code' => 400];
        }

        $extensions = ['xlsx', 'xls', 'csv'];
        if (!in_array($this->_memberLogic->extension, $extensions)) {
            $response = [
                'message' => 'Make sure you upload an excel file of .xls, .xlsx or .csv extension'
            ];
            return ['response' => $response, 'code' => 400];
        }

        if ($this->_memberLogic->size > 1048576) {
            $response = [
                'message' => 'Make sure the file does not exceed 1MB'
            ];
            return ['response' => $response, 'code' => 400];
        }

        return true;
    }
}
