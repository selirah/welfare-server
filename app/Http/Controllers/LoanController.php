<?php

namespace App\Http\Controllers;

use App\Models\LoanModel;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    private $_loanModel;

    public function __construct(LoanModel $loanModel)
    {
        $this->_loanModel = $loanModel;
    }

    // @route  POST api/v1/loans
    // @desc   add loan
    // @access Public
    public function grantLoan(Request $request)
    {
        $this->_loanModel->institutionId = $request->user()->institution_id;
        $this->_loanModel->staffId = trim($request->input('staff_id'));
        $this->_loanModel->amountLoaned = trim($request->input('amount_loaned'));
        $this->_loanModel->loanType = trim($request->input('loan_type'));
        $this->_loanModel->password = trim($request->input('password'));
        $this->_loanModel->time = trim($request->input('time'));
        $this->_loanModel->month = trim($request->input('month'));
        $this->_loanModel->year = trim($request->input('year'));
        $this->_loanModel->userId = $request->user()->id;
        $this->_loanModel->hashedPassword = $request->user()->password;

        $response = $this->_loanModel->grantLoan();
        return response()->json($response['response'], $response['code']);
    }

    // @route  PUT api/v1/loans/:id?
    // @desc   Update loan
    // @access Public
    public function updateLoan($id, Request $request)
    {
        $this->_loanModel->id = $id;
        $this->_loanModel->institutionId = $request->user()->institution_id;
        $this->_loanModel->staffId = trim($request->input('staff_id'));
        $this->_loanModel->amountPaid = trim($request->input('amount_paid'));
        $this->_loanModel->password = trim($request->input('password'));
        $this->_loanModel->month = trim($request->input('month'));
        $this->_loanModel->year = trim($request->input('year'));
        $this->_loanModel->userId = $request->user()->id;
        $this->_loanModel->hashedPassword = $request->user()->password;

        $response = $this->_loanModel->updateLoan();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/loans/:year
    // @desc   Get loans
    // @access Public
    public function getLoans(Request $request)
    {
        $this->_loanModel->month = trim($request->get('month'));
        $this->_loanModel->year = trim($request->get('year'));
        $this->_loanModel->institutionId = $request->user()->institution_id;

        $response = $this->_loanModel->getLoans();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/loans/:id
    // @desc   Get loan
    // @access Public
    public function getLoan($id)
    {
        $this->_loanModel->id = $id;

        $response = $this->_loanModel->getLoan();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/loans/summary
    // @desc   Get loans summary
    // @access Public
    public function getLoanSummary(Request $request)
    {
        $this->_loanModel->institutionId = $request->user()->institution_id;

        $response = $this->_loanModel->getLoanSummary();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/loans/member/loan/{staff_id}
    // @desc   Get loans with member id
    // @access Public
    public function getMemberLoans($staffId, Request $request)
    {
        $this->_loanModel->institutionId = $request->user()->institution_id;
        $this->_loanModel->staffId = $staffId;

        $response = $this->_loanModel->getMemberLoans();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/loans/calculate/yearly
    // @desc   Calculate yearly loans
    // @access Public
    public function getYearlyLoans(Request $request)
    {
        $this->_loanModel->institutionId = $request->user()->institution_id;
        $this->_loanModel->year = date('Y');

        $response = $this->_loanModel->getTotalAnnualLoans();
        return response()->json($response['response'], $response['code']);
    }

    // @route  DELETE api/v1/loans/:id
    // @desc   Delete loan
    // @access Public
    public function deleteLoan($id)
    {
        $this->_loanModel->id = $id;

        $response = $this->_loanModel->deleteLoan();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/loans/payments/loan/payment/:loan_id
    // @desc   Calculate loans payments
    // @access Public
    public function getLoanPayments($id)
    {
        $this->_loanModel->id = $id;

        $response = $this->_loanModel->getLoanPayments();
        return response()->json($response['response'], $response['code']);
    }
}
