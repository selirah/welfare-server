<?php

namespace App\Http\Controllers;

use App\Logic\MemberLogic;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    private $_memberLogic;

    public function __construct(MemberLogic $memberLogic)
    {
        $this->_memberLogic = $memberLogic;
    }

    // @route  GET api/v1/members
    // @desc   Get members
    // @access Public
    public function getMembers(Request $request)
    {
        $this->_memberLogic->institutionId = $request->user()->institution_id;

        $response = $this->_memberLogic->getMembers();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/members/:id
    // @desc   Get member
    // @access Public
    public function getMember($id)
    {
        $this->_memberLogic->id = $id;

        $response = $this->_memberLogic->getMember();
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/members
    // @desc   Create member
    // @access Public
    public function createMember(Request $request)
    {
        $this->_memberLogic->memberName = trim($request->input('name'));
        $this->_memberLogic->memberEmail = trim($request->input('email'));
        $this->_memberLogic->memberPhone = trim($request->input('phone'));
        $this->_memberLogic->staffId = trim($request->input('staff_id'));
        $this->_memberLogic->amount = trim($request->input('amount'));
        $this->_memberLogic->paymentType = trim($request->input('payment_type'));
        $this->_memberLogic->institutionId = trim($request->input('institution_id'));

        $response = $this->_memberLogic->createMember();
        return response()->json($response['response'], $response['code']);
    }

    // @route  PUT api/v1/members/:id
    // @desc   Update member
    // @access Public
    public function updateMember($id, Request $request)
    {
        $this->_memberLogic->memberName = trim($request->input('name'));
        $this->_memberLogic->memberEmail = trim($request->input('email'));
        $this->_memberLogic->memberPhone = trim($request->input('phone'));
        $this->_memberLogic->staffId = trim($request->input('staff_id'));
        $this->_memberLogic->amount = trim($request->input('amount'));
        $this->_memberLogic->paymentType = trim($request->input('payment_type'));
        $this->_memberLogic->institutionId = trim($request->input('institution_id'));
        $this->_memberLogic->id = $id;

        $response = $this->_memberLogic->updateMember();
        return response()->json($response['response'], $response['code']);
    }

    // @route  DELETE api/v1/members/:id
    // @desc   Delete member
    // @access Public
    public function deleteMember($id, Request $request)
    {
        $this->_memberLogic->id = $id;
        $this->_memberLogic->institutionId = $request->user()->institution_id;

        $response = $this->_memberLogic->deleteMember();
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/members/upload
    // @desc   Upload members
    // @access Public
    public function uploadMembers(Request $request)
    {
        $this->_memberLogic->hasFile = $request->hasFile('excel');
        $this->_memberLogic->excel = $request->file('excel');
        $this->_memberLogic->sheet = trim($request->input('sheet'));
        $this->_memberLogic->startRow = trim($request->input('start_row'));
        $this->_memberLogic->paymentType = trim($request->input('payment_type'));
        $this->_memberLogic->institutionId = $request->user()->institution_id;

        if ($this->_memberLogic->hasFile) {
            $this->_memberLogic->extension = $request->file('excel')->getClientOriginalExtension();
            $this->_memberLogic->size = $request->file('excel')->getSize();
            $this->_memberLogic->tmpPath = $request->file('excel')->getPathname();
        }

        $response = $this->_memberLogic->uploadMembers();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/members/count/total
    // @desc   Get total members
    // @access Public
    public function getTotalMembers(Request $request)
    {
        $this->_memberLogic->institutionId = $request->user()->institution_id;
        $response = $this->_memberLogic->getTotalMembers();
        return response()->json($response['response'], $response['code']);
    }
}
