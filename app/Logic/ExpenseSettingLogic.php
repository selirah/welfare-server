<?php

namespace App\Logic;

use App\Interfaces\ExpenseSettingInterface;
use App\Models\ExpenseSetting;
use App\Validations\ExpenseSettingModelValidation;
use Carbon\Carbon;
use stdClass;

class ExpenseSettingLogic implements ExpenseSettingInterface
{
    private $_expenseSetting;
    private $_validation;

    public $id;
    public $institutionId;
    public $parentId;
    public $type;

    public function __construct(ExpenseSetting $expenseSetting)
    {
        $this->_expenseSetting = $expenseSetting;
    }

    public function addExpenseSetting()
    {
        $this->_validation = new ExpenseSettingModelValidation($this);
        $validation = $this->_validation->__validateExpenseSetting();
        if ($validation !== true) {
            return $validation;
        }

        try {
            $payload = [
                'institution_id' => $this->institutionId,
                'parent_id' => $this->parentId,
                'type' => $this->type,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $this->_expenseSetting->_save($payload);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Expense setting successfully saved'
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function updateExpenseSetting()
    {
        $this->_validation = new ExpenseSettingModelValidation($this);
        $validation = $this->_validation->__validateExpenseSetting();
        if ($validation !== true) {
            return $validation;
        }

        try {
            $payload = [
                'parent_id' => $this->parentId,
                'type' => $this->type,
                'updated_at' => Carbon::now(),
            ];

            $this->_expenseSetting->_update($this->id, $payload);

            $settings = $this->_expenseSetting->_gets($this->institutionId);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Expense setting has successfully been updated',
                    'expense_settings' => $settings
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function deleteExpenseSetting()
    {
        try {
            $this->_expenseSetting->_delete($this->id);

            $settings = $this->_expenseSetting->_gets($this->institutionId);

            if ($settings->isEmpty()) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'successful',
                        'expense_settings' => []
                    ]
                ];
                return ['response' => $response, 'code' => 200];
            }

            $s = [];
            foreach ($settings as $setting) {
                $s[] = [
                    'id' => $setting->id,
                    'parent_id' => $setting->parent_id,
                    'parent' => $setting->parent_id !== 0 ? $this->_expenseSetting->_get($setting->parent_id)->type : $setting->type,
                    'type' => $setting->type,
                    'created_at' => $setting->created_at,
                    'updated_at' => $setting->updated_at
                ];
            }


            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Expense setting deleted successfully',
                    'expense_settings' => $s
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getExpenseSettings()
    {
        try {
            $settings = $this->_expenseSetting->_gets($this->institutionId);

            if ($settings->isEmpty()) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'successful',
                        'expense_settings' => []
                    ]
                ];
                return ['response' => $response, 'code' => 200];
            }

            $s = [];
            foreach ($settings as $setting) {
                $s[] = [
                    'id' => $setting->id,
                    'parent_id' => $setting->parent_id,
                    'parent' => $setting->parent_id !== 0 ? $this->_expenseSetting->_get($setting->parent_id)->type : $setting->type,
                    'type' => $setting->type,
                    'created_at' => $setting->created_at,
                    'updated_at' => $setting->updated_at
                ];
            }

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'expense_settings' => $s
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getExpenseSetting()
    {
        try {
            $setting = $this->_expenseSetting->_get($this->id);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'expense_setting' => $setting ? $setting : new stdClass()
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getExpenseSettingsWithParentId()
    {
        try {
            $settings = $this->_expenseSetting->_getWithParentId($this->institutionId, $this->parentId);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'expense_settings' => $settings->isNotEmpty() ? $settings : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
}
