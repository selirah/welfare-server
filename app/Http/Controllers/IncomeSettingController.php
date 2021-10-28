<?php

namespace App\Http\Controllers;

use App\Models\IncomeSettingModel;
use Illuminate\Http\Request;

class IncomeSettingController extends Controller
{
    private $_incomeSettingModel;

    public function __construct(IncomeSettingModel $incomeSettingModel)
    {
        $this->_incomeSettingModel = $incomeSettingModel;
    }

    // @route  POST api/v1/income-settings
    // @desc   set income
    // @access Public
    public function createIncomeSetting(Request $request)
    {
        $this->_incomeSettingModel->institutionId = $request->user()->institution_id;
        $this->_incomeSettingModel->type = trim($request->input('type'));
        $this->_incomeSettingModel->parentId = trim($request->input('parent_id'));

        $response = $this->_incomeSettingModel->addIncomeSetting();
        return response()->json($response['response'], $response['code']);
    }

    // @route  PUT api/v1/income-settings/:id
    // @desc   update income setting
    // @access Public
    public function updateIncomeSetting($id, Request $request)
    {
        $this->_incomeSettingModel->id = $id;
        $this->_incomeSettingModel->institutionId = $request->user()->institution_id;
        $this->_incomeSettingModel->type = trim($request->input('type'));
        $this->_incomeSettingModel->parentId = trim($request->input('parent_id'));

        $response = $this->_incomeSettingModel->updateIncomeSetting();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/income-settings
    // @desc   get income settings
    // @access Public
    public function getIncomeSettings(Request $request)
    {
        $this->_incomeSettingModel->institutionId = $request->user()->institution_id;

        $response = $this->_incomeSettingModel->getIncomeSettings();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/income-settings/:id
    // @desc   get income setting
    // @access Public
    public function getIncomeSetting($id)
    {
        $this->_incomeSettingModel->id = $id;

        $response = $this->_incomeSettingModel->getIncomeSetting();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/income-settings?parent_id=
    // @desc   get income settings with parent id
    // @access Public
    public function getIncomeSettingsWithParentId(Request $request)
    {
        $this->_incomeSettingModel->institutionId = $request->user()->institution_id;
        $this->_incomeSettingModel->parentId = trim($request->get('parent_id'));

        $response = $this->_incomeSettingModel->getIncomeSettingsWithParentId();
        return response()->json($response['response'], $response['code']);
    }

    // @route  DELETE api/v1/income-settings/:id
    // @desc   delete income setting
    // @access Public
    public function deleteIncomeSetting($id, Request $request)
    {
        $this->_incomeSettingModel->id = $id;
        $this->_incomeSettingModel->institutionId = $request->user()->institution_id;

        $response = $this->_incomeSettingModel->deleteIncomeSetting();
        return response()->json($response['response'], $response['code']);
    }
}
