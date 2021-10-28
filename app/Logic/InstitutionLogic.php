<?php

namespace App\Logic;

use App\Interfaces\InstitutionInterface;
use App\Models\Institution;
use App\Models\ExpenseSetting;
use App\Models\IncomeSetting;
use App\Models\DonationSetting;
use App\Validations\InstitutionValidation;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Exception;

class InstitutionLogic implements InstitutionInterface
{
    private $_institution;
    private $_validation;
    private $_expenseSetting;
    private $_incomeSetting;
    private $_donationSetting;

    public $id;
    public $userId;
    public $name;
    public $location;
    public $email;
    public $phone;
    public $senderId;
    public $apiKey;
    public $paymentType;
    public $logo;
    public $hasFile;
    public $extension;
    public $size;

    public function __construct(Institution $institution, ExpenseSetting $expressSetting, IncomeSetting $incomeSetting, DonationSetting $donationSetting)
    {
        $this->_institution = $institution;
        $this->_expenseSetting = $expressSetting;
        $this->_incomeSetting = $incomeSetting;
        $this->_donationSetting = $donationSetting;
    }

    public function getInstitution()
    {
        try {
            // fetch institution from DB
            $institution = $this->_institution->_get($this->id);

            if (!$institution) {
                return ['response' => null, 'code' => 200];
            }

            return ['response' => $institution, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createInstitution()
    {
        $this->_validation = new InstitutionValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateInstitution();
        if ($validation !== true) {
            return $validation;
        }

        try {
            // save institution
            $payload = [
                'user_id' => $this->userId,
                'name' => $this->name,
                'location' => $this->location,
                'email' => $this->email,
                'phone' => $this->phone,
                'sender_id' => $this->senderId,
                'api_key' => $this->apiKey,
                'payment_type' => $this->paymentType,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            $this->id = $this->_institution->_save($payload);

            // add new institution id to payload to return to user
            $payload['id'] = $this->id;

            // add expense, income, and donation setting and set institution name as first parent
            $setting = [
                'institution_id' => $this->id,
                'parent_id' => 0,
                'type' => $this->name,
            ];

            $this->_expenseSetting->_save($setting);
            $this->_incomeSetting->_save($setting);
            $this->_donationSetting->_save($setting);

            return ['response' => $this->id, 'code' => 201];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateInstitution()
    {
        $this->_validation = new InstitutionValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateInstitution();
        if ($validation !== true) {
            return $validation;
        }

        try {
            // update institution
            $payload = [
                'name' => $this->name,
                'location' => $this->location,
                'email' => $this->email,
                'phone' => $this->phone,
                'sender_id' => $this->senderId,
                'api_key' => $this->apiKey,
                'payment_type' => $this->paymentType,
                'updated_at' => Carbon::now()
            ];

            $this->_institution->_update($this->id, $payload);

            // update expense, donation, and income settings
            $setting = [
                'type' => $this->name,
            ];

            $this->_expenseSetting->_updateParent($setting);
            $this->_incomeSetting->_updateParent($setting);
            $this->_donationSetting->_updateParent($setting);

            return ['response' => null, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function deleteInstitution()
    {
        try {
            // delete institution
            $this->_institution->_delete($this->id);

            return ['response' => null, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function addLogo()
    {
        $this->_validation = new InstitutionValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateInstitutionLogo();
        if ($validation !== true) {
            return $validation;
        }

        $inst = $this->_institution->_getWithUserId($this->userId);

        try {
            // create random logo name
            $logoName = date('YmdHis') . '.' . $this->extension;

            // save file to disk
            Storage::disk('public')->put('logos/' . $logoName, File::get($this->logo));

            // get a logo url for frontend to use and save to DB
            $logoUrl = url('files/logos/' . $logoName);

            $payload = [
                'logo' => $logoUrl
            ];
            $this->_institution->_update($inst->id, $payload);

            return ['response' => null, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }
}
