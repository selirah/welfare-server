<?php

namespace App\Http\Controllers;

use App\Models\ExpenseModel;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    private $_expenseModel;

    public function __construct(ExpenseModel $expenseModel)
    {
        $this->_expenseModel = $expenseModel;
    }

    // @route  POST api/v1/expenses
    // @desc   add expense
    // @access Public
    public function addExpense(Request $request)
    {
        $this->_expenseModel->institutionId = $request->user()->institution_id;
        $this->_expenseModel->staffId = trim($request->input('staff_id'));
        $this->_expenseModel->typeId = trim($request->input('type'));
        $this->_expenseModel->amount = trim($request->input('amount'));
        $this->_expenseModel->description = trim($request->input('description'));
        $this->_expenseModel->month = trim($request->input('month'));
        $this->_expenseModel->year = trim($request->input('year'));
        $this->_expenseModel->userName = $request->user()->name;

        $response = $this->_expenseModel->addExpense();
        return response()->json($response['response'], $response['code']);
    }

    // @route  PUT api/v1/expenses/:id
    // @desc   update expense
    // @access Public
    public function updateExpense($id, Request $request)
    {
        $this->_expenseModel->id = $id;
        $this->_expenseModel->institutionId = $request->user()->institution_id;
        $this->_expenseModel->staffId = trim($request->input('staff_id'));
        $this->_expenseModel->typeId = trim($request->input('type'));
        $this->_expenseModel->amount = trim($request->input('amount'));
        $this->_expenseModel->description = trim($request->input('description'));
        $this->_expenseModel->month = trim($request->input('month'));
        $this->_expenseModel->year = trim($request->input('year'));
        $this->_expenseModel->userName = $request->user()->name;

        $response = $this->_expenseModel->updateExpense();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/expenses
    // @desc   Get expenses
    // @access Public
    public function getExpenses(Request $request)
    {
        $this->_expenseModel->institutionId = $request->user()->institution_id;
        $this->_expenseModel->month = trim($request->get('month'));
        $this->_expenseModel->year = trim($request->get('year'));

        $response = $this->_expenseModel->getExpenses();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/expenses/:id
    // @desc   Get expense
    // @access Public
    public function getExpense($id)
    {
        $this->_expenseModel->id = $id;

        $response = $this->_expenseModel->getExpense();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/expenses/summary
    // @desc   Get expense summary
    // @access Public
    public function getExpenseSummary(Request $request)
    {
        $this->_expenseModel->institutionId = $request->user()->institution_id;

        $response = $this->_expenseModel->getExpensesSummary();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/expenses/office
    // @desc   Get office expenses
    // @access Public
    public function getOfficeExpenses(Request $request)
    {
        $this->_expenseModel->institutionId = $request->user()->institution_id;
        $this->_expenseModel->month = trim($request->get('month'));
        $this->_expenseModel->year = trim($request->get('year'));

        $response = $this->_expenseModel->getOfficeExpenses();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/expenses/members
    // @desc   Get members expenses
    // @access Public
    public function getMembersExpenses(Request $request)
    {
        $this->_expenseModel->institutionId = $request->user()->institution_id;
        $this->_expenseModel->month = trim($request->get('month'));
        $this->_expenseModel->year = trim($request->get('year'));

        $response = $this->_expenseModel->getMembersExpenses();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/expenses/member/:staff_id=
    // @desc   Get member expenses
    // @access Public
    public function getMemberExpenses($staffId, Request $request)
    {
        $this->_expenseModel->staffId = $staffId;
        $this->_expenseModel->institutionId = $request->user()->institution_id;
        $this->_expenseModel->month = trim($request->get('month'));
        $this->_expenseModel->year = trim($request->get('year'));

        $response = $this->_expenseModel->getMemberExpenses();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/expenses/type/:type_id=
    // @desc   Get type expenses
    // @access Public
    public function getTypeExpenses($typeId, Request $request)
    {
        $this->_expenseModel->typeId = $typeId;
        $this->_expenseModel->institutionId = $request->user()->institution_id;
        $this->_expenseModel->month = trim($request->get('month'));
        $this->_expenseModel->year = trim($request->get('year'));

        $response = $this->_expenseModel->getTypeExpenses();
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/expenses/upload
    // @desc   upload expenses
    // @access Public
    public function uploadExpenses(Request $request)
    {
        $this->_expenseModel->month = trim($request->input('month'));
        $this->_expenseModel->typeId = trim($request->input('type'));
        $this->_expenseModel->year = trim($request->input('year'));
        $this->_expenseModel->hasFile = $request->hasFile('excel');
        $this->_expenseModel->excel = $request->file('excel');
        $this->_expenseModel->sheet = trim($request->input('sheet'));
        $this->_expenseModel->startRow = trim($request->input('start_row'));
        $this->_expenseModel->userName = $request->user()->name;
        $this->_expenseModel->institutionId = $request->user()->institution_id;

        if ($this->_expenseModel->hasFile) {
            $this->_expenseModel->extension = $request->file('excel')->getClientOriginalExtension();
            $this->_expenseModel->size = $request->file('excel')->getSize();
            $this->_expenseModel->tmpPath = $request->file('excel')->getPathname();
        }

        $response = $this->_expenseModel->uploadExpenses();
        return response()->json($response['response'], $response['code']);
    }

    // @route  DELETE api/v1/expenses/:id
    // @desc   Delete expense
    // @access Public
    public function deleteExpense($id)
    {
        $this->_expenseModel->id = $id;

        $response = $this->_expenseModel->deleteExpense();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/expenses/calculate/yearly
    // @desc   Calculate yearly expenses
    // @access Public
    public function getYearlyExpenses(Request $request)
    {
        $this->_expenseModel->institutionId = $request->user()->institution_id;
        $this->_expenseModel->year = trim($request->get('year'));

        $response = $this->_expenseModel->calculateYearlyTotalExpense();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/expenses/export/all
    // @desc   Export all expenses
    // @access Public
    public function exportAllExpenses(Request $request)
    {
        $this->_expenseModel->institutionId = $request->user()->institution_id;
        $this->_expenseModel->month = trim($request->get('month'));
        $this->_expenseModel->year = trim($request->get('year'));

        $response = $this->_expenseModel->exportAllExpenses();
        return response()->download($response['file'], $response['filename'], $response['headers'])->deleteFileAfterSend();
    }

    // @route  GET api/v1/expenses/export/office
    // @desc   Export office expenses
    // @access Public
    public function exportOfficeExpenses(Request $request)
    {
        $this->_expenseModel->institutionId = $request->user()->institution_id;
        $this->_expenseModel->month = trim($request->get('month'));
        $this->_expenseModel->year = trim($request->get('year'));

        $response = $this->_expenseModel->exportOfficeExpenses();
        return response()->download($response['file'], $response['filename'], $response['headers'])->deleteFileAfterSend();
    }

    // @route  GET api/v1/expenses/export/members
    // @desc   Export members expenses
    // @access Public
    public function exportMembersExpenses(Request $request)
    {
        $this->_expenseModel->institutionId = $request->user()->institution_id;
        $this->_expenseModel->month = trim($request->get('month'));
        $this->_expenseModel->year = trim($request->get('year'));

        $response = $this->_expenseModel->exportMembersExpenses();
        return response()->download($response['file'], $response['filename'], $response['headers'])->deleteFileAfterSend();
    }


    // @route  GET api/v1/expenses/export/members/staff_id=
    // @desc   Export members expenses
    // @access Public
    public function exportMemberExpenses($staffId, Request $request)
    {
        $this->_expenseModel->institutionId = $request->user()->institution_id;
        $this->_expenseModel->staffId = $staffId;
        $this->_expenseModel->month = trim($request->get('month'));
        $this->_expenseModel->year = trim($request->get('year'));

        $response = $this->_expenseModel->exportMemberExpenses();
        return response()->download($response['file'], $response['filename'], $response['headers'])->deleteFileAfterSend();
    }
}
