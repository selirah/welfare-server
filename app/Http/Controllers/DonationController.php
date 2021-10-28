<?php

namespace App\Http\Controllers;

use App\Models\DonationModel;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    private $_donationModel;

    public function __construct(DonationModel $donationModel)
    {
        $this->_donationModel = $donationModel;
    }

    // @route  POST api/v1/donations
    // @desc   add donation
    // @access Public
    public function addDonation(Request $request)
    {
        $this->_donationModel->institutionId = $request->user()->institution_id;
        $this->_donationModel->staffId = trim($request->input('staff_id'));
        $this->_donationModel->typeId = trim($request->input('type'));
        $this->_donationModel->amount = trim($request->input('amount'));
        $this->_donationModel->description = trim($request->input('description'));
        $this->_donationModel->month = trim($request->input('month'));
        $this->_donationModel->year = trim($request->input('year'));
        $this->_donationModel->userName = $request->user()->name;

        $response = $this->_donationModel->addDonation();
        return response()->json($response['response'], $response['code']);
    }

    // @route  PUT api/v1/donations/:id
    // @desc   update donation
    // @access Public
    public function updateDonation($id, Request $request)
    {
        $this->_donationModel->id = $id;
        $this->_donationModel->institutionId = $request->user()->institution_id;
        $this->_donationModel->staffId = trim($request->input('staff_id'));
        $this->_donationModel->typeId = trim($request->input('type'));
        $this->_donationModel->amount = trim($request->input('amount'));
        $this->_donationModel->description = trim($request->input('description'));
        $this->_donationModel->month = trim($request->input('month'));
        $this->_donationModel->year = trim($request->input('year'));
        $this->_donationModel->userName = $request->user()->name;

        $response = $this->_donationModel->updateDonation();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/donations
    // @desc   Get donations
    // @access Public
    public function getDonations(Request $request)
    {
        $this->_donationModel->institutionId = $request->user()->institution_id;
        $this->_donationModel->month = trim($request->get('month'));
        $this->_donationModel->year = trim($request->get('year'));

        $response = $this->_donationModel->getDonations();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/donations/:id
    // @desc   Get donation
    // @access Public
    public function getDonation($id)
    {
        $this->_donationModel->id = $id;

        $response = $this->_donationModel->getDonation();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/donations/summary
    // @desc   Get donation summary
    // @access Public
    public function getDonationSummary(Request $request)
    {
        $this->_donationModel->institutionId = $request->user()->institution_id;

        $response = $this->_donationModel->getDonationsSummary();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/donations/office
    // @desc   Get office donations
    // @access Public
    public function getOfficeDonations(Request $request)
    {
        $this->_donationModel->institutionId = $request->user()->institution_id;
        $this->_donationModel->month = trim($request->get('month'));
        $this->_donationModel->year = trim($request->get('year'));

        $response = $this->_donationModel->getOfficeDonations();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/donations/members
    // @desc   Get members donations
    // @access Public
    public function getMembersDonations(Request $request)
    {
        $this->_donationModel->institutionId = $request->user()->institution_id;
        $this->_donationModel->month = trim($request->get('month'));
        $this->_donationModel->year = trim($request->get('year'));

        $response = $this->_donationModel->getMembersDonations();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/donations/member/:staff_id=
    // @desc   Get member donations
    // @access Public
    public function getMemberDonations($staffId, Request $request)
    {
        $this->_donationModel->staffId = $staffId;
        $this->_donationModel->institutionId = $request->user()->institution_id;
        $this->_donationModel->month = trim($request->get('month'));
        $this->_donationModel->year = trim($request->get('year'));

        $response = $this->_donationModel->getMemberDonations();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/donations/type/:type_id=
    // @desc   Get type donations
    // @access Public
    public function getTypeDonations($typeId, Request $request)
    {
        $this->_donationModel->typeId = $typeId;
        $this->_donationModel->institutionId = $request->user()->institution_id;
        $this->_donationModel->month = trim($request->get('month'));
        $this->_donationModel->year = trim($request->get('year'));

        $response = $this->_donationModel->getTypeDonations();
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/donations/upload
    // @desc   upload donations
    // @access Public
    public function uploadDonations(Request $request)
    {
        $this->_donationModel->month = trim($request->input('month'));
        $this->_donationModel->typeId = trim($request->input('type'));
        $this->_donationModel->year = trim($request->input('year'));
        $this->_donationModel->hasFile = $request->hasFile('excel');
        $this->_donationModel->excel = $request->file('excel');
        $this->_donationModel->sheet = trim($request->input('sheet'));
        $this->_donationModel->startRow = trim($request->input('start_row'));
        $this->_donationModel->userName = $request->user()->name;
        $this->_donationModel->institutionId = $request->user()->institution_id;

        if ($this->_donationModel->hasFile) {
            $this->_donationModel->extension = $request->file('excel')->getClientOriginalExtension();
            $this->_donationModel->size = $request->file('excel')->getSize();
            $this->_donationModel->tmpPath = $request->file('excel')->getPathname();
        }

        $response = $this->_donationModel->uploadDonations();
        return response()->json($response['response'], $response['code']);
    }

    // @route  DELETE api/v1/donations/:id
    // @desc   Delete donation
    // @access Public
    public function deleteDonation($id)
    {
        $this->_donationModel->id = $id;

        $response = $this->_donationModel->deleteDonation();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/donations/calculate/yearly
    // @desc   Calculate yearly donations
    // @access Public
    public function getYearlyDonation(Request $request)
    {
        $this->_donationModel->institutionId = $request->user()->institution_id;
        $this->_donationModel->year = trim($request->get('year'));

        $response = $this->_donationModel->calculateYearlyTotalDonation();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/donations/export/all
    // @desc   Export all donations
    // @access Public
    public function exportAllDonations(Request $request)
    {
        $this->_donationModel->institutionId = $request->user()->institution_id;
        $this->_donationModel->month = trim($request->get('month'));
        $this->_donationModel->year = trim($request->get('year'));

        $response = $this->_donationModel->exportAllDonations();
        return response()->download($response['file'], $response['filename'], $response['headers'])->deleteFileAfterSend();
    }

    // @route  GET api/v1/donations/export/office
    // @desc   Export office donations
    // @access Public
    public function exportOfficeDonations(Request $request)
    {
        $this->_donationModel->institutionId = $request->user()->institution_id;
        $this->_donationModel->month = trim($request->get('month'));
        $this->_donationModel->year = trim($request->get('year'));

        $response = $this->_donationModel->exportOfficeDonations();
        return response()->download($response['file'], $response['filename'], $response['headers'])->deleteFileAfterSend();
    }

    // @route  GET api/v1/donations/export/members
    // @desc   Export members donations
    // @access Public
    public function exportMembersDonations(Request $request)
    {
        $this->_donationModel->institutionId = $request->user()->institution_id;
        $this->_donationModel->month = trim($request->get('month'));
        $this->_donationModel->year = trim($request->get('year'));

        $response = $this->_donationModel->exportMembersDonations();
        return response()->download($response['file'], $response['filename'], $response['headers'])->deleteFileAfterSend();
    }

    // @route  GET api/v1/donations/export/members/staff_id=
    // @desc   Export member donations
    // @access Public
    public function exportMemberDonations($staffId, Request $request)
    {
        $this->_donationModel->institutionId = $request->user()->institution_id;
        $this->_donationModel->staffId = $staffId;
        $this->_donationModel->month = trim($request->get('month'));
        $this->_donationModel->year = trim($request->get('year'));

        $response = $this->_donationModel->exportMemberDonations();
        return response()->download($response['file'], $response['filename'], $response['headers'])->deleteFileAfterSend();
    }
}
