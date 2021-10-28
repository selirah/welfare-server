<?php

namespace App\Validations;


use App\Logic\InstitutionLogic;

class InstitutionValidation
{
    private $_institutionLogic;

    public function __construct(InstitutionLogic $institutionLogic)
    {
        $this->_institutionLogic = $institutionLogic;
    }

    public function __validateInstitution()
    {
        if (
            empty($this->_institutionLogic->name) || empty($this->_institutionLogic->email) ||
            empty($this->_institutionLogic->phone) || empty($this->_institutionLogic->senderId) || empty($this->_institutionLogic->paymentType)
        ) {
            $response = [
                'message' => 'Name, Email, Phone, Payment type and Sender ID fields are all required'
            ];
            return ['response' => $response, 'code' => 400];
        }

        if (!filter_var($this->_institutionLogic->email, FILTER_VALIDATE_EMAIL)) {
            $response = [
                'message' => 'Provide a valid email address in the format john@doe.com'
            ];
            return ['response' => $response, 'code' => 400];
        }
        return true;
    }

    public function __validateInstitutionLogo()
    {
        if (!$this->_institutionLogic->hasFile) {
            $response = [
                'message' => 'Make sure you select a file to upload'
            ];
            return ['response' => $response, 'code' => 400];
        }

        $extensions = ['png', 'jpg', 'jpeg'];
        if (!in_array($this->_institutionLogic->extension, $extensions)) {
            $response = [
                'message' => 'Make sure you upload a logo of .png, .jpg, .jpeg extension'
            ];
            return ['response' => $response, 'code' => 400];
        }

        if ($this->_institutionLogic->size > 1048576) {
            $response = [
                'message' => 'Make sure the file does not exceed 1MB'
            ];
            return ['response' => $response, 'code' => 400];
        }

        return true;
    }
}
