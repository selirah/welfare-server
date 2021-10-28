<?php

namespace App\Logic;


use App\Interfaces\UtilityInterface;
use App\Models\PaymentType;
use App\Models\UniformPayment;
use App\Validations\UtilityModelValidation;
use Carbon\Carbon;

class UtilityLogic implements UtilityInterface
{
    private $_paymentType;
    private $_uniformPayment;
    private $_validation;

    public $institutionId;
    public $id;
    public $amount;
    public $paymentType;

    public function __construct(PaymentType $paymentType, UniformPayment $uniformPayment)
    {
        $this->_paymentType = $paymentType;
        $this->_uniformPayment = $uniformPayment;
    }

    public function setUniformPayment()
    {
        $this->_validation = new UtilityModelValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateUniformPayment();
        if ($validation !== true) {
            return $validation;
        }

        try {
            // set uniform payment amount for the institution.
            $payload = [
                'institution_id' => $this->institutionId,
                'amount' => $this->amount,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            $this->_uniformPayment->_save($payload);

            $uniform = $this->_uniformPayment->_get($this->institutionId);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Uniform payment successfully set',
                    'utility' => $uniform
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function updateUniformPayment()
    {
        $this->_validation = new UtilityModelValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateUniformPayment();
        if ($validation !== true) {
            return $validation;
        }

        try {
            // set uniform payment amount for the institution.
            $payload = [
                'amount' => $this->amount,
                'updated_at' => Carbon::now()
            ];
            $this->_uniformPayment->_update($this->id, $payload);

            $uniform = $this->_uniformPayment->_get($this->institutionId);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Uniform payment successfully updated',
                    'utility' => $uniform
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getPaymentTypes()
    {
        try {
            // fetch payments types from DB
            $types = $this->_paymentType->_gets();

            if ($types->isEmpty()) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'No records found'
                    ]
                ];
                return ['response' => $response, 'code' => 404];
            }

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'payment_types' => $types
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getUniformAmount()
    {
        try {
            // fetch uniform amount from DB
            $uniform = $this->_uniformPayment->_get($this->institutionId);

            if (!$uniform) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'No records found'
                    ]
                ];
                return ['response' => $response, 'code' => 404];
            }

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'utility' => $uniform
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
}
