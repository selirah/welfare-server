<?php

namespace App\Validations;


use App\Logic\UtilityLogic;

class UtilityValidation
{
    private $_utilityLogic;

    public function __construct(UtilityLogic $utilityLogic)
    {
        $this->_utilityLogic = $utilityLogic;
    }

    public function __validateUniformPayment()
    {
        if (empty($this->_utilityLogic->amount)) {
            $response = [
                'success' => false,
                'message' => 'The amount field is required'
            ];
            return ['response' => $response, 'code' => 400];
        }
        if ($this->_utilityLogic->paymentType != env('UNIFORM')) {
            $response = [
                'success' => false,
                'message' => 'You cannot perform this operation because your payment type is not set to Uniform'
            ];
            return ['response' => $response, 'code' => 400];
        }
        return true;
    }
}
