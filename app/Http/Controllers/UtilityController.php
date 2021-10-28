<?php

namespace App\Http\Controllers;

use App\Models\UtilityModel;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
    private $_utilityModel;

    public function __construct(UtilityModel $utilityModel)
    {
        $this->_utilityModel = $utilityModel;
    }

    // @route  GET api/v1/utility/payment-types
    // @desc   Get payment types
    // @access Public
    public function getPaymentTypes()
    {
        $response = $this->_utilityModel->getPaymentTypes();
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/set-uniform-payment
    // @desc   Set uniform payment
    // @access Public
    public function setUniformPayment(Request $request)
    {
        $this->_utilityModel->amount = trim($request->input('amount'));
        $this->_utilityModel->institutionId = $request->user()->institution_id;
        $this->_utilityModel->paymentType = trim($request->input('payment_type'));

        $response = $this->_utilityModel->setUniformPayment();
        return response()->json($response['response'], $response['code']);
    }

    // @route  PUT api/v1/update-uniform-payment/:id
    // @desc   Update uniform payment
    // @access Public
    public function updateUniformPayment($id, Request $request)
    {
        $this->_utilityModel->amount = trim($request->input('amount'));
        $this->_utilityModel->institutionId = $request->user()->institution_id;
        $this->_utilityModel->paymentType = trim($request->input('payment_type'));
        $this->_utilityModel->id = $id;

        $response = $this->_utilityModel->updateUniformPayment();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/utility/uniform-amount
    // @desc   Get Uniform Amount
    // @access Public
    public function getUniformAmount(Request $request)
    {
        $this->_utilityModel->institutionId = $request->user()->institution_id;
        $response = $this->_utilityModel->getUniformAmount();
        return response()->json($response['response'], $response['code']);
    }
}
