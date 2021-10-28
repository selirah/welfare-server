<?php

namespace App\Validations;


use App\Logic\UserLogic;

class UserValidation
{
    private $_userLogic;

    public function __construct(UserLogic $userLogic)
    {
        $this->_userLogic = $userLogic;
    }

    public function __validateUserRegistration()
    {
        if (empty($this->_userLogic->name) || empty($this->_userLogic->email) || empty($this->_userLogic->phone) || empty($this->_userLogic->password) || empty($this->_userLogic->confirmPass)) {
            $response = [
                'success' => false,
                'message' => 'All fields are required'
            ];
            return ['response' => $response, 'code' => 400];
        }

        if (!filter_var($this->_userLogic->email, FILTER_VALIDATE_EMAIL)) {
            $response = [
                'success' => false,
                'message' => 'Provide a valid email address in the format john@doe.com'
            ];
            return ['response' => $response, 'code' => 400];
        }

        if ($this->_userLogic->hasAgreed == false) {
            $response = [
                'success' => false,
                'message' => 'Make sure you agree to terms and conditions'
            ];
            return ['response' => $response, 'code' => 400];
        }

        if ($this->_userLogic->password != $this->_userLogic->confirmPass) {
            $response = [
                'success' => false,
                'message' => 'Passwords do not match'
            ];
            return ['response' => $response, 'code' => 400];
        }
        return true;
    }

    public function __validateUserActivation()
    {
        if (empty($this->_userLogic->code)) {
            $response = [
                'success' => false,
                'message' => 'Code field is required'
            ];
            return ['response' => $response, 'code' => 400];
        }
        return true;
    }

    public function __validateUserCodeResend()
    {
        if (empty($this->_userLogic->email)) {
            $response = [
                'success' => false,
                'message' => 'Email field is required'
            ];
            return ['response' => $response, 'code' => 400];
        }
        if (!filter_var($this->_userLogic->email, FILTER_VALIDATE_EMAIL)) {
            $response = [
                'success' => false,
                'message' => 'Provide a valid email address in the format john@doe.com'
            ];
            return ['response' => $response, 'code' => 400];
        }
        return true;
    }

    public function __validateUserLogin()
    {
        if (empty($this->_userLogic->email) || empty($this->_userLogic->password)) {
            $response = [
                'success' => false,
                'message' => 'All fields are required.'
            ];
            return ['response' => $response, 'code' => 400];
        }
        if (!filter_var($this->_userLogic->email, FILTER_VALIDATE_EMAIL)) {
            $response = [
                'success' => false,
                'message' => 'Provide a valid email address in the format john@doe.com'
            ];
            return ['response' => $response, 'code' => 400];
        }
        return true;
    }

    public function __validateUserProfile()
    {
        if (empty($this->_userLogic->name) || empty($this->_userLogic->email) || empty($this->_userLogic->phone)) {
            $response = [
                'success' => false,
                'message' => 'All fields are required'
            ];
            return ['response' => $response, 'code' => 400];
        }
        if (!filter_var($this->_userLogic->email, FILTER_VALIDATE_EMAIL)) {
            $response = [
                'success' => false,
                'message' => 'Provide a valid email address in the format john@doe.com'
            ];
            return ['response' => $response, 'code' => 400];
        }

        return true;
    }

    public function __validateUserPassword()
    {
        if (empty($this->_userLogic->password) || empty($this->_userLogic->oldPassword) || empty($this->_userLogic->confirmPass)) {
            $response = [
                'success' => false,
                'message' => 'All fields are required'
            ];
            return ['response' => $response, 'code' => 400];
        }
        if ($this->_userLogic->password != $this->_userLogic->confirmPass) {
            $response = [
                'success' => false,
                'message' => 'Passwords do not match'
            ];
            return ['response' => $response, 'code' => 400];
        }

        return true;
    }
}
