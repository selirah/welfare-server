<?php

namespace App\Http\Controllers;

use App\Models\AdminModel;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private $_adminModel;

    public function __construct(AdminModel $adminModel)
    {
        $this->_adminModel = $adminModel;
    }

    // @route  GET api/v1/admin/clients
    // @desc   Get Clients
    // @access Private
    public function getClients(Request $request)
    {
        $this->_adminModel->year = trim($request->get('year'));

        $response = $this->_adminModel->getClients();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/admin/clients/impersonate/client
    // @desc   Impersonate Client
    // @access Private
    public function impersonateClient(Request $request)
    {
        $this->_adminModel->userId = $request->input('user_id');
        $this->_adminModel->adminId = $request->user()->id;
        $request->user()->token()->revoke();
        $request->user()->token()->delete();

        $response = $this->_adminModel->impersonateClient($request);
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/admin/clients/impersonate/admin
    // @desc   Impersonate Admin
    // @access Private
    public function impersonateAdmin(Request $request)
    {
        $this->_adminModel->adminId = $request->input('admin_id');
        $request->user()->token()->revoke();
        $request->user()->token()->delete();

        $response = $this->_adminModel->impersonateAdmin($request);
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/admin/clients/exports
    // @desc   Export Clients
    // @access Private
    public function exportClients(Request $request)
    {
        $year = trim($request->get('year'));
        $this->_adminModel->year = !empty($year) ? $year : date('Y');

        $response = $this->_adminModel->exportClients();
        return response()->download($response['file'], $response['filename'], $response['headers'])->deleteFileAfterSend();
    }


    // @route  GET api/v1/admin/welfare/get-totals
    // @desc   Get Totals
    // @access Private
    public function getTotals(Request $request)
    {
        $this->_adminModel->institutionId = $request->user()->institution_id;
        $this->_adminModel->year = trim($request->get('year'));

        $response = $this->_adminModel->getTotals();
        return response()->json($response['response'], $response['code']);
    }


    // @route  GET api/v1/admin/welfare/get-yearly-summary
    // @desc   Get Monthly Summary
    // @access Private
    public function getYearlySummary(Request $request)
    {
        $this->_adminModel->institutionId = $request->user()->institution_id;
        $this->_adminModel->year = trim($request->get('year'));

        $response = $this->_adminModel->getYearlySummary();
        return response()->json($response['response'], $response['code']);
    }
}
