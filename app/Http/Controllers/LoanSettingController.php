<?php

namespace App\Http\Controllers;

use App\Models\LoanSettingModel;
use Illuminate\Http\Request;

class LoanSettingController extends Controller
{
    private $_loanSettingModel;

    public function __construct(LoanSettingModel $loanSettingModel)
    {
        $this->_loanSettingModel = $loanSettingModel;
    }

    // @route  POST api/v1/loan-settings
    // @desc   set loan
    // @access Public
    public function setLoan(Request $request)
    {
        $this->_loanSettingModel->institutionId = $request->user()->institution_id;
        $this->_loanSettingModel->type = trim($request->input('type'));
        $this->_loanSettingModel->rate = trim($request->input('rate'));
        $this->_loanSettingModel->minMonth = trim($request->input('min_month'));
        $this->_loanSettingModel->maxMonth = trim($request->input('max_month'));

        $response = $this->_loanSettingModel->setLoan();
        return response()->json($response['response'], $response['code']);
    }

    // @route  PUT api/v1/loan-settings/:id
    // @desc   update loan setting
    // @access Public
    public function updateLoanSetting($id, Request $request)
    {
        $this->_loanSettingModel->id = $id;
        $this->_loanSettingModel->institutionId = $request->user()->institution_id;
        $this->_loanSettingModel->type = trim($request->input('type'));
        $this->_loanSettingModel->rate = trim($request->input('rate'));
        $this->_loanSettingModel->minMonth = trim($request->input('min_month'));
        $this->_loanSettingModel->maxMonth = trim($request->input('max_month'));

        $response = $this->_loanSettingModel->updateLoanSetting();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/loan-settings
    // @desc   get loan settings
    // @access Public
    public function getLoanSettings(Request $request)
    {
        $this->_loanSettingModel->institutionId = $request->user()->institution_id;

        $response = $this->_loanSettingModel->getLoanSettings();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/loan-settings/:id
    // @desc   get loan setting
    // @access Public
    public function getLoanSetting($id)
    {
        $this->_loanSettingModel->id = $id;

        $response = $this->_loanSettingModel->getLoanSetting();
        return response()->json($response['response'], $response['code']);
    }

    // @route  DELETE api/v1/loan-settings/:id
    // @desc   delete loan setting
    // @access Public
    public function deleteLoanSetting($id, Request $request)
    {
        $this->_loanSettingModel->id = $id;
        $this->_loanSettingModel->institutionId = $request->user()->institution_id;

        $response = $this->_loanSettingModel->deleteLoanSetting();
        return response()->json($response['response'], $response['code']);
    }
}
