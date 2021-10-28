<?php

namespace App\Logic;


use App\Interfaces\DonationSettingInterface;
use App\Models\DonationSetting;
use App\Validations\DonationSettingModelValidation;
use Carbon\Carbon;
use stdClass;

class DonationSettingLogic implements DonationSettingInterface
{
    private $_donationSetting;
    private $_validation;

    public $id;
    public $institutionId;
    public $parentId;
    public $type;


    public function __construct(DonationSetting $donationSetting)
    {
        $this->_donationSetting = $donationSetting;
    }

    public function addDonationSetting()
    {
        $this->_validation = new DonationSettingModelValidation($this);
        $validation = $this->_validation->__validateDonationSetting();
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

            $this->_donationSetting->_save($payload);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Donation setting successfully saved'
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function updateDonationSetting()
    {
        $this->_validation = new DonationSettingModelValidation($this);
        $validation = $this->_validation->__validateDonationSetting();
        if ($validation !== true) {
            return $validation;
        }

        try {
            $payload = [
                'parent_id' => $this->parentId,
                'type' => $this->type,
                'updated_at' => Carbon::now(),
            ];

            $this->_donationSetting->_update($this->id, $payload);

            $settings = $this->_donationSetting->_gets($this->institutionId);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Donation setting has successfully been updated',
                    'donation_settings' => $settings
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function deleteDonationSetting()
    {
        try {
            $this->_donationSetting->_delete($this->id);

            $settings = $this->_donationSetting->_gets($this->institutionId);

            if ($settings->isEmpty()) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'successful',
                        'donation_settings' => []
                    ]
                ];
                return ['response' => $response, 'code' => 200];
            }

            $s = [];
            foreach ($settings as $setting) {
                $s[] = [
                    'id' => $setting->id,
                    'parent_id' => $setting->parent_id,
                    'parent' => $setting->parent_id !== 0 ? $this->_donationSetting->_get($setting->parent_id)->type : $setting->type,
                    'type' => $setting->type,
                    'created_at' => $setting->created_at,
                    'updated_at' => $setting->updated_at
                ];
            }

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Donation setting deleted successfully',
                    'donation_settings' => $settings->isNotEmpty() ? $settings : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getDonationSettings()
    {
        try {
            $settings = $this->_donationSetting->_gets($this->institutionId);

            if ($settings->isEmpty()) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'successful',
                        'donation_settings' => []
                    ]
                ];
                return ['response' => $response, 'code' => 200];
            }

            $s = [];
            foreach ($settings as $setting) {
                $s[] = [
                    'id' => $setting->id,
                    'parent_id' => $setting->parent_id,
                    'parent' => $setting->parent_id !== 0 ? $this->_donationSetting->_get($setting->parent_id)->type : $setting->type,
                    'type' => $setting->type,
                    'created_at' => $setting->created_at,
                    'updated_at' => $setting->updated_at
                ];
            }

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'donation_settings' => $s
                ]
            ];


            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getDonationSetting()
    {
        try {
            $setting = $this->_donationSetting->_get($this->id);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'donation_setting' => $setting ? $setting : new stdClass()
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getDonationSettingsWithParentId()
    {
        try {
            $settings = $this->_donationSetting->_getWithParentId($this->institutionId, $this->parentId);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'donation_settings' => $settings->isNotEmpty() ? $settings : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
}
