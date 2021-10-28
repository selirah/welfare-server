<?php

/**
 * Created by PhpStorm.
 * User: selirah
 * Date: 7/6/2019
 * Time: 8:35 PM
 */

namespace App\Logic;


use App\Interfaces\LoanSettingInterface;
use App\Models\LoanSetting;
use App\Validations\LoanSettingModelValidation;
use Carbon\Carbon;

class LoanSettingLogic implements LoanSettingInterface
{
    private $_loanSetting;
    private $_validation;

    public $id;
    public $institutionId;
    public $type;
    public $rate;
    public $minMonth;
    public $maxMonth;


    public function __construct(LoanSetting $loanSetting)
    {
        $this->_loanSetting = $loanSetting;
    }

    public function setLoan()
    {
        $this->_validation = new LoanSettingModelValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateLoanSetting();
        if ($validation !== true) {
            return $validation;
        }

        try {
            // save loan settings
            $payload = [
                'institution_id' => $this->institutionId,
                'type' => $this->type,
                'rate' => $this->rate,
                'min_month' => $this->minMonth,
                'max_month' => $this->maxMonth,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            $this->_loanSetting->_save($payload);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Loan has successfully been set'
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function updateLoanSetting()
    {
        $this->_validation = new LoanSettingModelValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateLoanSetting();
        if ($validation !== true) {
            return $validation;
        }

        try {
            // save loan settings
            $payload = [
                'type' => $this->type,
                'rate' => $this->rate,
                'min_month' => $this->minMonth,
                'max_month' => $this->maxMonth,
                'updated_at' => Carbon::now()
            ];

            $this->_loanSetting->_update($this->id, $payload);

            $settings = $this->_loanSetting->_gets($this->institutionId);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Loan has successfully been updated',
                    'loan_settings' => $settings
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getLoanSettings()
    {
        try {
            // get loans settings
            $settings = $this->_loanSetting->_gets($this->institutionId);

            if ($settings->isEmpty()) {
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
                    'loan_settings' => $settings
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getLoanSetting()
    {
        try {
            // get loans setting
            $setting = $this->_loanSetting->_get($this->id);

            if (!$setting) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'No record found'
                    ]
                ];
                return ['response' => $response, 'code' => 404];
            }

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'loan_setting' => $setting
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function deleteLoanSetting()
    {
        try {

            // delete loan setting
            $this->_loanSetting->_delete($this->id);

            $settings = $this->_loanSetting->_gets($this->institutionId);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Loan setting deleted successfully',
                    'loan_settings' => $settings
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
}
