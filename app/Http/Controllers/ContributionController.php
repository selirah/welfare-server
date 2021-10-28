<?php

namespace App\Http\Controllers;

use App\Models\ContributionModel;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    private $_contributionModel;

    public function __construct(ContributionModel $contributionModel)
    {
        $this->_contributionModel = $contributionModel;
    }


    // @route  POST api/v1/contributions
    // @desc   Add contribution
    // @access Public
    public function addContribution(Request $request)
    {
        $this->_contributionModel->staffId = trim($request->input('staff_id'));
        $this->_contributionModel->amount = trim($request->input('amount'));
        $this->_contributionModel->month = trim($request->input('month'));
        $this->_contributionModel->year = trim($request->input('year'));
        $this->_contributionModel->paymentType = trim($request->input('payment_type'));
        $this->_contributionModel->institutionId = $request->user()->institution_id;

        $response = $this->_contributionModel->addContribution();
        return response()->json($response['response'], $response['code']);
    }

    // @route  PUT api/v1/contributions/:id?
    // @desc   Update contribution
    // @access Public
    public function updateContribution($id, Request $request)
    {
        $this->_contributionModel->id = $id;
        $this->_contributionModel->staffId = trim($request->input('staff_id'));
        $this->_contributionModel->amount = trim($request->input('amount'));
        $this->_contributionModel->month = trim($request->input('month'));
        $this->_contributionModel->year = trim($request->input('year'));
        $this->_contributionModel->paymentType = trim($request->input('payment_type'));
        $this->_contributionModel->institutionId = $request->user()->institution_id;

        $response = $this->_contributionModel->editContribution();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/contributions
    // @desc   Get contributions
    // @access Public
    public function getContributions(Request $request)
    {
        $this->_contributionModel->institutionId = $request->user()->institution_id;

        $response = $this->_contributionModel->getContributions();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/contributions/:id
    // @desc   Get contribution
    // @access Public
    public function getContribution($id)
    {
        $this->_contributionModel->id = $id;

        $response = $this->_contributionModel->getContribution();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/contributions/summary
    // @desc   Get contributions summary
    // @access Public
    public function getContributionSummary(Request $request)
    {
        $this->_contributionModel->institutionId = $request->user()->institution_id;

        $response = $this->_contributionModel->getContributionSummary();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/contributions/{month}/{year}
    // @desc   Get contributions with month and year
    // @access Public
    public function getContributionWithMonthAndYear($month, $year, Request $request)
    {
        $this->_contributionModel->institutionId = $request->user()->institution_id;
        $this->_contributionModel->month = $month;
        $this->_contributionModel->year = $year;

        $response = $this->_contributionModel->getContributionsWithMonthAndYear();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/contributions/member/contribution/{staff_id}
    // @desc   Get contributions with member id
    // @access Public
    public function getMemberContributions($staffId, Request $request)
    {
        $this->_contributionModel->institutionId = $request->user()->institution_id;
        $this->_contributionModel->staffId = $staffId;

        $response = $this->_contributionModel->getMemberContributions();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/contributions/member/{staff_id}/{month}/{year}
    // @desc   Get contributions with member id, month and year
    // @access Public
    public function getContributionWithMemberIdMonthAndYear($staffId, $month, $year, Request $request)
    {
        $this->_contributionModel->institutionId = $request->user()->institution_id;
        $this->_contributionModel->staffId = $staffId;
        $this->_contributionModel->month = $month;
        $this->_contributionModel->year = $year;

        $response = $this->_contributionModel->getContributionWithMemberIdMonthAndYear();
        return response()->json($response['response'], $response['code']);
    }

    // @route  DELETE api/v1/contributions/:id
    // @desc   Delete contribution
    // @access Public
    public function deleteContribution($id)
    {
        $this->_contributionModel->id = $id;

        $response = $this->_contributionModel->deleteContribution();
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/contributions/:id
    // @desc   upload contributions
    // @access Public
    public function uploadContributions(Request $request)
    {
        $this->_contributionModel->month = trim($request->input('month'));
        $this->_contributionModel->year = trim($request->input('year'));
        $this->_contributionModel->hasFile = $request->hasFile('excel');
        $this->_contributionModel->excel = $request->file('excel');
        $this->_contributionModel->sheet = trim($request->input('sheet'));
        $this->_contributionModel->startRow = trim($request->input('start_row'));
        $this->_contributionModel->paymentType = trim($request->input('payment_type'));

        if ($this->_contributionModel->hasFile) {
            $this->_contributionModel->extension = $request->file('excel')->getClientOriginalExtension();
            $this->_contributionModel->size = $request->file('excel')->getSize();
            $this->_contributionModel->tmpPath = $request->file('excel')->getPathname();
        }

        $this->_contributionModel->institutionId = $request->user()->institution_id;

        $response = $this->_contributionModel->uploadContributions();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/contributions/calculate/yearly
    // @desc   Calculate yearly contribution
    // @access Public
    public function getYearlyContributions(Request $request)
    {
        $this->_contributionModel->institutionId = $request->user()->institution_id;
        $this->_contributionModel->year = date('Y');

        $response = $this->_contributionModel->calculateYearlyTotalContributions();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/contributions/export/:month/:year
    // @desc   Export monthly contributions to excel
    // @access Public
    public function exportContributions(Request $request)
    {
        $this->_contributionModel->institutionId = $request->user()->institution_id;
        $this->_contributionModel->month =  trim($request->get('month'));
        $this->_contributionModel->year = trim($request->get('year'));

        $response = $this->_contributionModel->exportContributions();
        return response()->download($response['file'], $response['filename'], $response['headers'])->deleteFileAfterSend();
    }

    // @route  GET api/v1/contributions/members/no-contributions/:month/:year
    // @desc   Get members who hasn't contributed
    // @access Public
    public function getMembersWhoHasNotContributed($month, $year, Request $request)
    {
        $this->_contributionModel->institutionId = $request->user()->institution_id;
        $this->_contributionModel->month = $month;
        $this->_contributionModel->year = $year;

        $response = $this->_contributionModel->getMembersWithNoContributions();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/contributions/export/member/:staff_id
    // @desc   Export member contributions to excel
    // @access Public
    public function exportMemberContributions($staffId, Request $request)
    {
        $this->_contributionModel->institutionId = $request->user()->institution_id;
        $this->_contributionModel->staffId =  $staffId;
        $this->_contributionModel->month =  trim($request->get('month'));
        $this->_contributionModel->year = trim($request->get('year'));

        $response = $this->_contributionModel->exportMemberContributions();
        return response()->download($response['file'], $response['filename'], $response['headers'])->deleteFileAfterSend();
    }
}
