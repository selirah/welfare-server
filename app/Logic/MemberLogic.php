<?php

namespace App\Logic;


use App\Helpers\Helper;
use App\Interfaces\MemberInterface;
use App\Models\FixedPayment;
use App\Models\Member;
use App\Models\PaymentType;
use App\Validations\MemberValidation;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;

class MemberLogic implements MemberInterface
{
    private $_member;
    private $_paymentType;
    private $_fixedPayment;
    private $_validation;

    public $institutionId;
    public $paymentType;
    public $id;
    public $staffId;
    public $memberName;
    public $memberPhone;
    public $memberEmail;
    public $amount;
    public $hasFile;
    public $tmpPath;
    public $excel;
    public $extension;
    public $size;
    public $sheet;
    public $startRow;


    public function __construct(Member $member, PaymentType $paymentType, FixedPayment $fixedPayment)
    {
        $this->_member = $member;
        $this->_paymentType = $paymentType;
        $this->_fixedPayment = $fixedPayment;
    }

    public function getMembers()
    {
        try {
            // fetch members from DB
            $members = $this->_member->_gets($this->institutionId);

            return ['response' => $members, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getMember()
    {
        try {
            // fetch member from DB
            $member = $this->_member->_get($this->id);

            if (!$member) {
                return ['response' => null, 'code' => 404];
            }

            $MEMBER = [
                'id' => $member->id,
                'institution_id' => $member->institution_id,
                'member_staff_id' => $member->member_staff_id,
                'member_name' => $member->member_name,
                'member_phone' => $member->member_phone,
                'member_email' => $member->member_email,
                'created_at' => $member->created_at,
                'updated_at' => $member->updated_at
            ];

            $fixed = $this->_fixedPayment->_get($member->institution_id, $member->member_staff_id);

            if ($fixed) {
                $MEMBER['amount'] = $fixed->amount;
            }

            return ['response' => $MEMBER, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createMember()
    {
        $this->_validation = new MemberValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateMember();
        if ($validation !== true) {
            return $validation;
        }

        try {
            // save member
            $payload = [
                'institution_id' => $this->institutionId,
                'member_staff_id' => $this->staffId,
                'member_name' => $this->memberName,
                'member_email' => $this->memberEmail,
                'member_phone' => Helper::sanitizePhone($this->memberPhone),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            $this->id = $this->_member->_save($payload);


            // save amount member will pay when the fixed-rate payment type is selected by the institution
            if ($this->paymentType == env('FIXED_RATE')) {

                $fixedRate = [
                    'institution_id' => $this->institutionId,
                    'member_staff_id' => $this->staffId,
                    'amount' => $this->amount,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                $this->_fixedPayment->_save($fixedRate);
            }

            return ['response' => null, 'code' => 201];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateMember()
    {
        $this->_validation = new MemberValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateMember();
        if ($validation !== true) {
            return $validation;
        }

        try {
            // update member
            $payload = [
                'member_staff_id' => $this->staffId,
                'member_name' => $this->memberName,
                'member_email' => $this->memberEmail,
                'member_phone' => Helper::sanitizePhone($this->memberPhone),
                'updated_at' => Carbon::now()
            ];

            $this->id = $this->_member->_update($this->id, $payload);

            // update amount member will pay when the fixed-rate payment type is selected by the institution
            if ($this->paymentType == env('FIXED_RATE')) {


                // check if member has their fixed rates set
                $fixed = $this->_fixedPayment->_get($this->institutionId, $this->staffId);
                if (!$fixed) {
                    // insert into DB
                    $fixedRate = [
                        'institution_id' => $this->institutionId,
                        'member_staff_id' => $this->staffId,
                        'amount' => $this->amount,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                    $this->_fixedPayment->_save($fixedRate);
                } else {
                    // update
                    $fixedRate = [
                        'amount' => $this->amount,
                        'updated_at' => Carbon::now()
                    ];

                    $this->_fixedPayment->_update($this->institutionId, $this->staffId, $fixedRate);
                }
            }
            return ['response' => null, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function deleteMember()
    {
        try {
            // delete member
            $this->_member->_delete($this->id);

            return ['response' => null, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function uploadMembers()
    {
        $this->_validation = new MemberValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateMembersUpload();
        if ($validation !== true) {
            return $validation;
        }

        try {

            // tear the excel sheet apart
            $fileType = IOFactory::identify($this->tmpPath);
            $reader = IOFactory::createReader($fileType);
            $spreadsheet = $reader->load($this->tmpPath);

            $sheet = $spreadsheet->getSheet($this->sheet - 1);
            $sheetData = $sheet->toArray(null, true, true, true);

            // clean the data from the excel sheet
            $cleanData = [];
            for ($row = $this->startRow; $row <= count($sheetData); $row++) {
                $cleanData[] = $sheetData[$row];
            }

            // save data
            $members = [];
            $fixedRate = [];
            foreach ($cleanData as $c) {
                if ($c['A'] != null || $c['A'] == '') {
                    // check if member already exist
                    if ($this->_member->_getWithStaffId($this->institutionId, $c['A'])) {
                        $response = [
                            'message' => 'Staff ID ' . $c['A'] . ' already exists'
                        ];
                        return ['response' => $response, 'code' => 400];
                    }
                    // also check if institution has fixed-rated as their payment type and validate excel
                    if ($this->paymentType == env('FIXED_RATE')) {
                        // check if there is filled column for amount
                        if ($c['E'] == null || $c['E'] == '' || empty($c['E'])) {
                            $response = [
                                'message' => 'The choice of payment option by the institution demands that you include the fixed amount by each member in the E column of the excel'
                            ];
                            return ['response' => $response, 'code' => 400];
                        }
                        $fixedRate[] = [
                            'institution_id' => $this->institutionId,
                            'member_staff_id' => $c['A'],
                            'amount' => $c['E'],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }
                    $members[] = [
                        'institution_id' => $this->institutionId,
                        'member_staff_id' => $c['A'],
                        'member_name' => $c['B'],
                        'member_email' => $c['C'],
                        'member_phone' => Helper::sanitizePhone($c['D']),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
            }
            $this->_member->_save($members);
            $this->_fixedPayment->_save($fixedRate);

            return ['response' => null, 'code' => 201];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getTotalMembers()
    {
        try {
            // get total members
            $total = $this->_member->_getTotal($this->institutionId);

            return ['response' => $total, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }
}
