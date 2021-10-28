<?php

namespace App\Validations;


use App\Logic\DonationSettingLogic;

class DonationSettingValidation
{
    private $_donationSettingLogic;

    public function __construct(DonationSettingLogic $donationSettingLogic)
    {
        $this->_donationSettingLogic = $donationSettingLogic;
    }

    public function __validateDonationSetting()
    {
        if (empty($this->_donationSettingLogic->type) || empty($this->_donationSettingLogic->parentId)) {
            $response = [
                'success' => false,
                'message' => 'All fields are required'
            ];
            return ['response' => $response, 'code' => 400];
        }

        return true;
    }
}
