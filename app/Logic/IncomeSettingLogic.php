<?php

namespace App\Logic;

use App\Interfaces\IncomeSettingInterface;
use App\Models\IncomeSetting;
use App\Validations\IncomeSettingModelValidation;
use Carbon\Carbon;
use stdClass;

class IncomeSettingLogic implements IncomeSettingInterface
{
    private $_incomeSetting;
    private $_validation;

    public $id;
    public $institutionId;
    public $parentId;
    public $type;

    public function __construct(IncomeSetting $incomeSetting)
    {
        $this->_incomeSetting = $incomeSetting;
    }

    public function addIncomeSetting()
    {
        $this->_validation = new IncomeSettingModelValidation($this);
        $validation = $this->_validation->__validateIncomeSetting();
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

            $this->_incomeSetting->_save($payload);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Income setting successfully saved'
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function updateIncomeSetting()
    {
        $this->_validation = new IncomeSettingModelValidation($this);
        $validation = $this->_validation->__validateIncomeSetting();
        if ($validation !== true) {
            return $validation;
        }

        try {
            $payload = [
                'parent_id' => $this->parentId,
                'type' => $this->type,
                'updated_at' => Carbon::now(),
            ];

            $this->_incomeSetting->_update($this->id, $payload);

            $settings = $this->_incomeSetting->_gets($this->institutionId);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Income setting has successfully been updated',
                    'income_settings' => $settings
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function deleteIncomeSetting()
    {
        try {
            $this->_incomeSetting->_delete($this->id);

            $settings = $this->_incomeSetting->_gets($this->institutionId);

            if ($settings->isEmpty()) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'successful',
                        'income_settings' => []
                    ]
                ];
                return ['response' => $response, 'code' => 200];
            }

            $s = [];
            foreach ($settings as $setting) {
                $s[] = [
                    'id' => $setting->id,
                    'parent_id' => $setting->parent_id,
                    'parent' => $setting->parent_id !== 0 ? $this->_incomeSetting->_get($setting->parent_id)->type : $setting->type,
                    'type' => $setting->type,
                    'created_at' => $setting->created_at,
                    'updated_at' => $setting->updated_at
                ];
            }

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Income setting deleted successfully',
                    'income_settings' => $s
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getIncomeSettings()
    {
        try {
            $settings = $this->_incomeSetting->_gets($this->institutionId);

            if ($settings->isEmpty()) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'successful',
                        'income_settings' => []
                    ]
                ];
                return ['response' => $response, 'code' => 200];
            }

            $s = [];
            foreach ($settings as $setting) {
                $s[] = [
                    'id' => $setting->id,
                    'parent_id' => $setting->parent_id,
                    'parent' => $setting->parent_id !== 0 ? $this->_incomeSetting->_get($setting->parent_id)->type : $setting->type,
                    'type' => $setting->type,
                    'created_at' => $setting->created_at,
                    'updated_at' => $setting->updated_at
                ];
            }

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'income_settings' => $s
                ]
            ];


            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getIncomeSetting()
    {
        try {
            $setting = $this->_incomeSetting->_get($this->id);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'income_setting' => $setting ? $setting : new stdClass()
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getIncomeSettingsWithParentId()
    {
        try {
            $settings = $this->_incomeSetting->_getWithParentId($this->institutionId, $this->parentId);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'income_settings' => $settings->isNotEmpty() ? $settings : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
}
