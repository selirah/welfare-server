<?php

namespace App\Http\Controllers;

use App\Models\ExpenseSettingModel;
use Illuminate\Http\Request;

class ExpenseSettingController extends Controller
{
    private $_expenseSettingModel;

    public function __construct(ExpenseSettingModel $expenseSettingModel)
    {
        $this->_expenseSettingModel = $expenseSettingModel;
    }

    // @route  POST api/v1/expense-settings
    // @desc   set expense
    // @access Public
    public function createExpenseSetting(Request $request)
    {
        $this->_expenseSettingModel->institutionId = $request->user()->institution_id;
        $this->_expenseSettingModel->type = trim($request->input('type'));
        $this->_expenseSettingModel->parentId = trim($request->input('parent_id'));

        $response = $this->_expenseSettingModel->addExpenseSetting();
        return response()->json($response['response'], $response['code']);
    }

    // @route  PUT api/v1/expense-settings/:id
    // @desc   update expense setting
    // @access Public
    public function updateExpenseSetting($id, Request $request)
    {
        $this->_expenseSettingModel->id = $id;
        $this->_expenseSettingModel->institutionId = $request->user()->institution_id;
        $this->_expenseSettingModel->type = trim($request->input('type'));
        $this->_expenseSettingModel->parentId = trim($request->input('parent_id'));

        $response = $this->_expenseSettingModel->updateExpenseSetting();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/expense-settings
    // @desc   get expense settings
    // @access Public
    public function getExpenseSettings(Request $request)
    {
        $this->_expenseSettingModel->institutionId = $request->user()->institution_id;

        $response = $this->_expenseSettingModel->getExpenseSettings();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/expense-settings/:id
    // @desc   get expense setting
    // @access Public
    public function getExpenseSetting($id)
    {
        $this->_expenseSettingModel->id = $id;

        $response = $this->_expenseSettingModel->getExpenseSetting();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/expense-settings?parent_id=
    // @desc   get expense settings with parent id
    // @access Public
    public function getExpenseSettingsWithParentId(Request $request)
    {
        $this->_expenseSettingModel->institutionId = $request->user()->institution_id;
        $this->_expenseSettingModel->parentId = trim($request->get('parent_id'));

        $response = $this->_expenseSettingModel->getExpenseSettingsWithParentId();
        return response()->json($response['response'], $response['code']);
    }

    // @route  DELETE api/v1/expense-settings/:id
    // @desc   delete expense setting
    // @access Public
    public function deleteExpenseSetting($id, Request $request)
    {
        $this->_expenseSettingModel->id = $id;
        $this->_expenseSettingModel->institutionId = $request->user()->institution_id;

        $response = $this->_expenseSettingModel->deleteExpenseSetting();
        return response()->json($response['response'], $response['code']);
    }
}
