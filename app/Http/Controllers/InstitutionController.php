<?php

namespace App\Http\Controllers;

use App\Logic\InstitutionLogic;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    private $_institutionLogic;

    public function __construct(InstitutionLogic $institutionLogic)
    {
        $this->_institutionLogic = $institutionLogic;
    }

    // @route  GET api/v1/institution/:id
    // @desc   Get institution
    // @access Public
    public function getInstitution($id)
    {
        $this->_institutionLogic->id = $id;

        $response = $this->_institutionLogic->getInstitution();
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/institution
    // @desc   Create institution
    // @access Public
    public function createInstitution(Request $request)
    {
        $this->_institutionLogic->name = trim($request->input('name'));
        $this->_institutionLogic->email = trim($request->input('email'));
        $this->_institutionLogic->phone = trim($request->input('phone'));
        $this->_institutionLogic->location = trim($request->input('location'));
        $this->_institutionLogic->senderId = trim($request->input('sender_id'));
        $this->_institutionLogic->apiKey = trim($request->input('api_key'));
        $this->_institutionLogic->paymentType = trim($request->input('payment_type'));
        $this->_institutionLogic->userId = $request->user()->id;

        $response = $this->_institutionLogic->createInstitution();
        return response()->json($response['response'], $response['code']);
    }

    // @route  PUT api/v1/institution/:id
    // @desc   Update institution
    // @access Public
    public function updateInstitution($id, Request $request)
    {
        $this->_institutionLogic->name = trim($request->input('name'));
        $this->_institutionLogic->email = trim($request->input('email'));
        $this->_institutionLogic->phone = trim($request->input('phone'));
        $this->_institutionLogic->location = trim($request->input('location'));
        $this->_institutionLogic->senderId = trim($request->input('sender_id'));
        $this->_institutionLogic->apiKey = trim($request->input('api_key'));
        $this->_institutionLogic->paymentType = trim($request->input('payment_type'));
        $this->_institutionLogic->id = $id;

        $response = $this->_institutionLogic->updateInstitution();
        return response()->json($response['response'], $response['code']);
    }

    // @route  DELETE api/v1/institution/:id
    // @desc   Delete institution
    // @access Public
    public function deleteInstitution($id)
    {
        $this->_institutionLogic->id = $id;

        $response = $this->_institutionLogic->deleteInstitution();
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/institution/logo
    // @desc   Add institution logo
    // @access Public
    public function addLogo(Request $request)
    {
        $this->_institutionLogic->userId = $request->user()->id;
        $this->_institutionLogic->hasFile = $request->hasFile('file');
        $this->_institutionLogic->logo = $request->file('file');

        if ($this->_institutionLogic->hasFile) {
            $this->_institutionLogic->extension = $request->file('file')->getClientOriginalExtension();
            $this->_institutionLogic->size = $request->file('file')->getSize();
        }

        $response = $this->_institutionLogic->addLogo();
        return response()->json($response['response'], $response['code']);
    }
}
