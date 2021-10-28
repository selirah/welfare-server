<?php

namespace App\Http\Controllers;

use App\Models\DonationSettingModel;
use Illuminate\Http\Request;

class DonationSettingController extends Controller
{
    private $_donationSettingModel;

    public function __construct(DonationSettingModel $donationSettingModel)
    {
        $this->_donationSettingModel = $donationSettingModel;
    }

    // @route  POST api/v1/donation-settings
    // @desc   set donation
    // @access Public
    public function createDonationSetting(Request $request)
    {
        $this->_donationSettingModel->institutionId = $request->user()->institution_id;
        $this->_donationSettingModel->type = trim($request->input('type'));
        $this->_donationSettingModel->parentId = trim($request->input('parent_id'));

        $response = $this->_donationSettingModel->addDonationSetting();
        return response()->json($response['response'], $response['code']);
    }

    // @route  PUT api/v1/donation-settings/:id
    // @desc   update donation setting
    // @access Public
    public function updateDonationSetting($id, Request $request)
    {
        $this->_donationSettingModel->id = $id;
        $this->_donationSettingModel->institutionId = $request->user()->institution_id;
        $this->_donationSettingModel->type = trim($request->input('type'));
        $this->_donationSettingModel->parentId = trim($request->input('parent_id'));

        $response = $this->_donationSettingModel->updateDonationSetting();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/donation-settings
    // @desc   get donation settings
    // @access Public
    public function getDonationSettings(Request $request)
    {
        $this->_donationSettingModel->institutionId = $request->user()->institution_id;

        $response = $this->_donationSettingModel->getDonationSettings();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/donation-settings/:id
    // @desc   get donation setting
    // @access Public
    public function getDonationSetting($id)
    {
        $this->_donationSettingModel->id = $id;

        $response = $this->_donationSettingModel->getDonationSetting();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/donation-settings?parent_id=
    // @desc   get donation settings with parent id
    // @access Public
    public function getDonatonSettingsWithParentId(Request $request)
    {
        $this->_donationSettingModel->institutionId = $request->user()->institution_id;
        $this->_donationSettingModel->parentId = trim($request->get('parent_id'));

        $response = $this->_donationSettingModel->getDonationSettingsWithParentId();
        return response()->json($response['response'], $response['code']);
    }

    // @route  DELETE api/v1/donation-settings/:id
    // @desc   delete donation setting
    // @access Public
    public function deleteDonationSetting($id, Request $request)
    {
        $this->_donationSettingModel->id = $id;
        $this->_donationSettingModel->institutionId = $request->user()->institution_id;

        $response = $this->_donationSettingModel->deleteDonationSetting();
        return response()->json($response['response'], $response['code']);
    }
}
