<?php

namespace App\Http\Controllers;

use App\Models\IncomeModel;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    private $_incomeModel;

    public function __construct(IncomeModel $incomeModel)
    {
        $this->_incomeModel = $incomeModel;
    }

    // @route  POST api/v1/incomes
    // @desc   add income
    // @access Public
    public function addIncome(Request $request)
    {
        $this->_incomeModel->institutionId = $request->user()->institution_id;
        $this->_incomeModel->staffId = trim($request->input('staff_id'));
        $this->_incomeModel->typeId = trim($request->input('type'));
        $this->_incomeModel->amount = trim($request->input('amount'));
        $this->_incomeModel->description = trim($request->input('description'));
        $this->_incomeModel->month = trim($request->input('month'));
        $this->_incomeModel->year = trim($request->input('year'));
        $this->_incomeModel->userName = $request->user()->name;

        $response = $this->_incomeModel->addIncome();
        return response()->json($response['response'], $response['code']);
    }

    // @route  PUT api/v1/incomes/:id
    // @desc   update income
    // @access Public
    public function updateIncome($id, Request $request)
    {
        $this->_incomeModel->id = $id;
        $this->_incomeModel->institutionId = $request->user()->institution_id;
        $this->_incomeModel->staffId = trim($request->input('staff_id'));
        $this->_incomeModel->typeId = trim($request->input('type'));
        $this->_incomeModel->amount = trim($request->input('amount'));
        $this->_incomeModel->description = trim($request->input('description'));
        $this->_incomeModel->month = trim($request->input('month'));
        $this->_incomeModel->year = trim($request->input('year'));
        $this->_incomeModel->userName = $request->user()->name;

        $response = $this->_incomeModel->updateIncome();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/incomes
    // @desc   Get incomes
    // @access Public
    public function getIncomes(Request $request)
    {
        $this->_incomeModel->institutionId = $request->user()->institution_id;
        $this->_incomeModel->month = trim($request->get('month'));
        $this->_incomeModel->year = trim($request->get('year'));

        $response = $this->_incomeModel->getIncomes();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/incomes/:id
    // @desc   Get income
    // @access Public
    public function getIncome($id)
    {
        $this->_incomeModel->id = $id;

        $response = $this->_incomeModel->getIncome();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/incomes/summary
    // @desc   Get income summary
    // @access Public
    public function getIncomeSummary(Request $request)
    {
        $this->_incomeModel->institutionId = $request->user()->institution_id;

        $response = $this->_incomeModel->getIncomesSummary();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/incomes/office
    // @desc   Get office revenue
    // @access Public
    public function getOfficeIncomes(Request $request)
    {
        $this->_incomeModel->institutionId = $request->user()->institution_id;
        $this->_incomeModel->month = trim($request->get('month'));
        $this->_incomeModel->year = trim($request->get('year'));

        $response = $this->_incomeModel->getOfficeIncomes();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/incomes/members
    // @desc   Get members incomes
    // @access Public
    public function getMembersIncomes(Request $request)
    {
        $this->_incomeModel->institutionId = $request->user()->institution_id;
        $this->_incomeModel->month = trim($request->get('month'));
        $this->_incomeModel->year = trim($request->get('year'));

        $response = $this->_incomeModel->getMembersIncomes();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/incomes/member/:staff_id=
    // @desc   Get member incomes
    // @access Public
    public function getMemberIncomes($staffId, Request $request)
    {
        $this->_incomeModel->staffId = $staffId;
        $this->_incomeModel->institutionId = $request->user()->institution_id;
        $this->_incomeModel->month = trim($request->get('month'));
        $this->_incomeModel->year = trim($request->get('year'));

        $response = $this->_incomeModel->getMemberIncomes();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/incomes/type/:type_id=
    // @desc   Get type incomes
    // @access Public
    public function getTypeIncomes($typeId, Request $request)
    {
        $this->_incomeModel->typeId = $typeId;
        $this->_incomeModel->institutionId = $request->user()->institution_id;
        $this->_incomeModel->month = trim($request->get('month'));
        $this->_incomeModel->year = trim($request->get('year'));

        $response = $this->_incomeModel->getTypeIncomes();
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/expenses/upload
    // @desc   upload expenses
    // @access Public
    public function uploadIncomes(Request $request)
    {
        $this->_incomeModel->month = trim($request->input('month'));
        $this->_incomeModel->typeId = trim($request->input('type'));
        $this->_incomeModel->year = trim($request->input('year'));
        $this->_incomeModel->hasFile = $request->hasFile('excel');
        $this->_incomeModel->excel = $request->file('excel');
        $this->_incomeModel->sheet = trim($request->input('sheet'));
        $this->_incomeModel->startRow = trim($request->input('start_row'));
        $this->_incomeModel->userName = $request->user()->name;
        $this->_incomeModel->institutionId = $request->user()->institution_id;

        if ($this->_incomeModel->hasFile) {
            $this->_incomeModel->extension = $request->file('excel')->getClientOriginalExtension();
            $this->_incomeModel->size = $request->file('excel')->getSize();
            $this->_incomeModel->tmpPath = $request->file('excel')->getPathname();
        }

        $response = $this->_incomeModel->uploadIncomes();
        return response()->json($response['response'], $response['code']);
    }

    // @route  DELETE api/v1/incomes/:id
    // @desc   Delete income
    // @access Public
    public function deleteIncome($id)
    {
        $this->_incomeModel->id = $id;

        $response = $this->_incomeModel->deleteIncome();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/incomes/calculate/yearly
    // @desc   Calculate yearly income
    // @access Public
    public function getYearlyIncome(Request $request)
    {
        $this->_incomeModel->institutionId = $request->user()->institution_id;
        $this->_incomeModel->year = trim($request->get('year'));

        $response = $this->_incomeModel->calculateYearlyTotalIncome();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/incomes/export/all
    // @desc   Export all incomes
    // @access Public
    public function exportAllIncomes(Request $request)
    {
        $this->_incomeModel->institutionId = $request->user()->institution_id;
        $this->_incomeModel->month = trim($request->get('month'));
        $this->_incomeModel->year = trim($request->get('year'));

        $response = $this->_incomeModel->exportAllIncomes();
        return response()->download($response['file'], $response['filename'], $response['headers'])->deleteFileAfterSend();
    }

    // @route  GET api/v1/incomes/export/office
    // @desc   Export office incomes
    // @access Public
    public function exportOfficeIncomes(Request $request)
    {
        $this->_incomeModel->institutionId = $request->user()->institution_id;
        $this->_incomeModel->month = trim($request->get('month'));
        $this->_incomeModel->year = trim($request->get('year'));

        $response = $this->_incomeModel->exportOfficeIncomes();
        return response()->download($response['file'], $response['filename'], $response['headers'])->deleteFileAfterSend();
    }

    // @route  GET api/v1/incomes/export/members
    // @desc   Export members incomes
    // @access Public
    public function exportMembersIncomes(Request $request)
    {
        $this->_incomeModel->institutionId = $request->user()->institution_id;
        $this->_incomeModel->month = trim($request->get('month'));
        $this->_incomeModel->year = trim($request->get('year'));

        $response = $this->_incomeModel->exportMembersIncomes();
        return response()->download($response['file'], $response['filename'], $response['headers'])->deleteFileAfterSend();
    }

    // @route  GET api/v1/incomes/export/members/staff_id=
    // @desc   Export member incomes
    // @access Public
    public function exportMemberIncomes($staffId, Request $request)
    {
        $this->_incomeModel->institutionId = $request->user()->institution_id;
        $this->_incomeModel->staffId = $staffId;
        $this->_incomeModel->month = trim($request->get('month'));
        $this->_incomeModel->year = trim($request->get('year'));

        $response = $this->_incomeModel->exportMemberIncomes();
        return response()->download($response['file'], $response['filename'], $response['headers'])->deleteFileAfterSend();
    }
}
