<?php

namespace App\Logic;


use App\Interfaces\ContributionInterface;
use App\Models\Contribution;
use App\Models\FixedPayment;
use App\Models\Member;
use App\Models\PaymentType;
use App\Models\UniformPayment;
use App\Validations\ContributionValidation;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ContributionLogic implements ContributionInterface
{
    private $_contribution;
    private $_member;
    private $_paymentType;
    private $_fixedPayment;
    private $_uniformPayment;
    private $_validation;

    public $id;
    public $staffId;
    public $month;
    public $year;
    public $amount;
    public $institutionId;
    public $paymentType;
    public $hasFile;
    public $tmpPath;
    public $excel;
    public $extension;
    public $size;
    public $sheet;
    public $startRow;

    public function __construct(
        Contribution $contribution,
        Member $member,
        PaymentType $paymentType,
        FixedPayment $fixedPayment,
        UniformPayment $uniformPayment
    ) {
        $this->_contribution = $contribution;
        $this->_member = $member;
        $this->_paymentType = $paymentType;
        $this->_fixedPayment = $fixedPayment;
        $this->_uniformPayment = $uniformPayment;
    }

    public function addContribution()
    {
        $this->_validation = new ContributionValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateContribution();
        if ($validation !== true) {
            return $validation;
        }

        // there are 3 types of payment depending on what the institution chose
        // if the institution chose a uniform contribution, it means we will just
        // insert each member with the same amount the institution has as default amount

        // if the institution selected the fixed-rated type, we will insert based on what
        // the member has set to contribute every month

        // the default is when the institution selected the non-uniform type where that
        // one the user entering the contribution must enter the staff ID and the amount of the member

        try {
            // let's switch the case
            switch ($this->paymentType) {
                case env('UNIFORM'):

                    // we need to get the uniform amount set by the institution
                    $uniform = $this->_uniformPayment->_get($this->institutionId);
                    if (!$uniform) {
                        $response = [
                            'success' => false,
                            'message' => 'Your choice of payment type requires you to set the uniform amount.'
                        ];
                        return ['response' => $response, 'code' => 400];
                    }

                    // we must also fetch all the members
                    $members = $this->_member->_gets($this->institutionId);
                    if ($members->isEmpty()) {
                        $response = [
                            'success' => false,
                            'message' => 'Make sure you have added members to perform this operation'
                        ];
                        return ['response' => $response, 'code' => 404];
                    }

                    // now we must prepare the data for saving
                    $payload = [];
                    foreach ($members as $member) {
                        $payload[] = [
                            'institution_id' => $this->institutionId,
                            'member_staff_id' => $member->member_staff_id,
                            'month' => $this->month,
                            'year' => $this->year,
                            'date' => date('Y-m-d'),
                            'amount' => $uniform->amount,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }
                    $this->_contribution->_save($payload);

                    $response = [
                        'success' => true,
                        'message' => 'Operation conducted successfully'
                    ];

                    return ['response' => $response, 'code' => 201];

                    break;
                case env('FIXED_RATE'):

                    // we must fetch all the members
                    $members = $this->_member->_gets($this->institutionId);
                    if ($members->isEmpty()) {
                        $response = [
                            'success' => false,
                            'message' => 'Make sure you have added members to perform this operation'
                        ];
                        return ['response' => $response, 'code' => 404];
                    }

                    // now we must prepare the data for saving
                    $payload = [];
                    foreach ($members as $member) {
                        // we have to get the amount the member has promised to contribute every month
                        $fixed = $this->_fixedPayment->_get($this->institutionId, $member->member_staff_id);
                        if ($fixed) {
                            $payload[] = [
                                'institution_id' => $this->institutionId,
                                'member_staff_id' => $member->member_staff_id,
                                'month' => $this->month,
                                'year' => $this->year,
                                'date' => date('Y-m-d'),
                                'amount' => $fixed->amount,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
                    }
                    $this->_contribution->_save($payload);

                    $response = [
                        'success' => true,
                        'message' => 'Operation conducted successfully'
                    ];

                    return ['response' => $response, 'code' => 201];

                    break;
                case env('NON_UNIFORM'):
                    // this option is performed one at a time

                    $member = $this->_member->_getWithStaffId($this->institutionId, $this->staffId);

                    if (!$member) {
                        $response = [
                            'success' => false,
                            'message' => 'This staff ID is invalid'
                        ];
                        return ['response' => $response, 'code' => 400];
                    }

                    $payload = [
                        'institution_id' => $this->institutionId,
                        'member_staff_id' => $this->staffId,
                        'month' => $this->month,
                        'year' => $this->year,
                        'date' => date('Y-m-d'),
                        'amount' => $this->amount,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                    $this->_contribution->_save($payload);

                    $response = [
                        'success' => true,
                        'message' => 'Operation conducted successfully'
                    ];

                    return ['response' => $response, 'code' => 201];

                    break;
                default:
                    exit('Error somewhere');
            }
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function editContribution()
    {
        $this->_validation = new ContributionValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateContribution();
        if ($validation !== true) {
            return $validation;
        }

        try {
            // let's switch the case
            switch ($this->paymentType) {
                case env('UNIFORM'):

                    // we need to get the uniform amount set by the institution
                    $uniform = $this->_uniformPayment->_get($this->institutionId);
                    if (!$uniform) {
                        $response = [
                            'success' => false,
                            'message' => 'Your choice of payment type requires you to set the uniform amount.'
                        ];
                        return ['response' => $response, 'code' => 400];
                    }

                    // we have to fetch contributions with the specified month and year by the user
                    $contributions = $this->_contribution->_getsWithMonthAndYear($this->institutionId, $this->month, $this->year);
                    if ($contributions->isEmpty()) {
                        $response = [
                            'success' => false,
                            'message' => 'No contribution was added on the specified month and year'
                        ];
                        return ['response' => $response, 'code' => 404];
                    }

                    // we need to get the ids of the contributions to make update
                    $payload = [];
                    $ids = [];
                    foreach ($contributions as $contribution) {
                        $ids[] = $contribution->id;
                    }
                    $payload[] = [
                        'month' => $this->month,
                        'year' => $this->year,
                        'amount' => $uniform->amount,
                        'updated_at' => Carbon::now()
                    ];

                    $this->_contribution->_updateMultiple($ids, $payload);

                    $response = [
                        'success' => true,
                        'message' => 'Operation conducted successfully'
                    ];

                    return ['response' => $response, 'code' => 200];

                    break;
                case env('FIXED_RATE'):

                    // we have to fetch contributions with the specified month and year by the user
                    $contributions = $this->_contribution->_getsWithMonthAndYear($this->institutionId, $this->month, $this->year);
                    if ($contributions->isEmpty()) {
                        $response = [
                            'success' => false,
                            'message' => 'No contribution was added on the specified month and year'
                        ];
                        return ['response' => $response, 'code' => 404];
                    }

                    // now we must prepare the data for update
                    $payload = [];
                    foreach ($contributions as $contribution) {
                        // we have to get the amount the member has promised to contribute every month
                        $fixed = $this->_fixedPayment->_get($this->institutionId, $contribution->member_staff_id);
                        if ($fixed) {
                            $payload = [
                                'month' => $this->month,
                                'year' => $this->year,
                                'amount' => $fixed->amount,
                                'updated_at' => Carbon::now()
                            ];
                        }

                        $this->_contribution->_update($contribution->id, $payload);
                    }

                    $response = [
                        'success' => true,
                        'message' => 'Operation conducted successfully'
                    ];

                    return ['response' => $response, 'code' => 201];

                    break;
                case env('NON_UNIFORM'):
                    // this option is performed one at a time
                    $payload = [
                        'month' => $this->month,
                        'year' => $this->year,
                        'amount' => $this->amount,
                        'updated_at' => Carbon::now()
                    ];
                    $this->_contribution->_update($this->id, $payload);

                    $response = [
                        'success' => true,
                        'message' => 'Operation conducted successfully'
                    ];

                    return ['response' => $response, 'code' => 201];

                    break;
                default:
                    exit('Error somewhere');
            }
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getContributionSummary()
    {
        try {
            // get contribution summary
            $summary = $this->_contribution->_getSummary($this->institutionId);

            if ($summary->isEmpty()) {
                $response = [
                    'success' => false,
                    'message' => 'No contributions have been made yet'
                ];
                return ['response' => $response, 'code' => 404];
            }

            // in order to get members who have not paid for that month, we need a list of the members too
            $members = $this->_member->_gets($this->institutionId);

            $membersWhoHaveNotPaid = 0;
            $SUMMARY = [];

            foreach ($summary as $s) {

                // check if member has paid or not for the month and year in question
                foreach ($members as $member) {
                    $contribution = $this->_contribution->_getWithMemberIdMonthAndYear($this->institutionId, $member->member_staff_id, $s->month, $s->year);
                    if (!$contribution) {
                        // let's add those who haven't paid
                        $membersWhoHaveNotPaid++;
                    }
                }

                $SUMMARY[] = [
                    'month' => $s->month,
                    'year' => $s->year,
                    'total' => $s->total,
                    'amount' => $s->amount,
                    'not_contributed' => $membersWhoHaveNotPaid
                ];
            }

            $response = [
                'summary' => $SUMMARY
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getContributions()
    {
        try {
            // fetch contributions
            $contributions = $this->_contribution->_gets($this->institutionId);

            return ['response' => $contributions, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getContribution()
    {
        try {
            // fetch contribution with the id supplied
            $contribution = $this->_contribution->_get($this->id);

            if (!$contribution) {
                return ['response' => null, 'code' => 200];
            }

            return ['response' => $contribution, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getContributionsWithMonthAndYear()
    {
        try {

            // fetch contributions with month and year supplied
            $contributions = $this->_contribution->_getsWithMonthAndYear($this->institutionId, $this->month, $this->year);

            return ['response' => $contributions, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getMemberContributions()
    {
        try {
            // fetch contributions with member ID supplied
            $contributions = $this->_contribution->_getsWithMemberId($this->institutionId, $this->staffId);

            return ['response' => $contributions, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getContributionWithMemberIdMonthAndYear()
    {
        try {

            // fetch contribution with member ID, month and year supplied
            $contribution = $this->_contribution->_getWithMemberIdMonthAndYear($this->institutionId, $this->staffId, $this->month, $this->year);

            if (!$contribution) {
                return ['response' => null, 'code' => 404];
            }

            return ['response' => $contribution, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function deleteContribution()
    {
        try {

            // delete contribution
            $this->_contribution->_delete($this->id);

            return ['response' => null, 'code' => 204];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function uploadContributions()
    {
        // check if payment type is non-uniform
        if ($this->paymentType != env('NON_UNIFORM')) {
            $response = [
                'success' => false,
                'message' => 'Only Non-Uniform payment type institutions can perform this function'
            ];
            return ['response' => $response, 'code' => 400];
        }

        $this->_validation = new ContributionValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateContributionsUpload();
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

            $contributions = [];
            foreach ($cleanData as $c) {
                if ($c['A'] != null || $c['A'] == '' && $c['B'] != null || $c['B']) {
                    // check if member already exist
                    if (!$this->_member->_getWithStaffId($this->institutionId, $c['A'])) {
                        $response = [
                            'success' => false,
                            'message' => 'Staff ID ' . $c['A'] . ' does not exist'
                        ];
                        return ['response' => $response, 'code' => 400];
                    }

                    $contributions[] = [
                        'institution_id' => $this->institutionId,
                        'member_staff_id' => $c['A'],
                        'month' => $this->month,
                        'year' => $this->year,
                        'date' => date('Y-m-d'),
                        'amount' => $c['B'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
            }

            $this->_contribution->_save($contributions);

            return ['response' => null, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function calculateYearlyTotalContributions()
    {
        try {
            $sum = $this->_contribution->_calculateYearlyTotal($this->institutionId, $this->year);
            return ['response' => $sum, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function exportContributions()
    {
        try {

            // fetch contributions with month and year supplied
            $contributions = $this->_contribution->_getsWithMonthAndYear($this->institutionId, $this->month, $this->year);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $type = 'xlsx';

            $sheet->getStyle('A1:H1')->getFont()->setBold(true);
            $sheet->setCellValue('A1', 'MONTH');
            $sheet->setCellValue('B1', 'YEAR');
            $sheet->setCellValue('C1', 'STAFF ID');
            $sheet->setCellValue('D1', 'NAME');
            $sheet->setCellValue('E1', 'PHONE');
            $sheet->setCellValue('F1', 'AMOUNT (GHS)');
            $sheet->setCellValue('G1', 'DATE');

            $row = 2;
            foreach ($contributions as $contribution) {
                $sheet->setCellValue('A' . $row, $contribution->month);
                $sheet->setCellValue('B' . $row, $contribution->year);
                $sheet->setCellValue('C' . $row, $contribution->member_staff_id);
                $sheet->setCellValue('D' . $row, strtoupper($contribution->member_name));
                $sheet->setCellValueExplicit('E' . $row, $contribution->member_phone, DataType::TYPE_STRING);
                $sheet->setCellValue('F' . $row, number_format((float)$contribution->amount, 2, '.', ''));
                $sheet->setCellValueExplicit('G' . $row, $contribution->date, DataType::TYPE_STRING);
                $row++;

                $sheet->setCellValue("E$row", "Total")->getStyle("E" . (count($contributions) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("F$row", "=SUM(F2:F" . ($row - 1) . ")")->getStyle("F" . (count($contributions) + 2))->getFont()->setBold(true);
            }

            $fileName = 'contributions_' . $this->month . '_' . $this->year . '.' . $type;
            $writer = new Xlsx($spreadsheet);

            $writer->save(public_path() . '/files/' . $fileName);
            $headers = [
                'Content-Type: application/vnd.ms-excel',
                'Content-Transfer-Encoding: Binary',
                'Content-Disposition: attachment; filename=' . $fileName

            ];

            $file = public_path() . "/files/" . $fileName;

            $response = [
                'file' => $file,
                'filename' => $fileName,
                'headers' => $headers
            ];

            return $response;
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getMembersWithNoContributions()
    {
        try {

            // fetch members
            $members = $this->_member->_gets($this->institutionId);
            $MEMBERS = [];
            foreach ($members as $member) {
                $contribution = $this->_contribution->_getWithMemberIdMonthAndYear($this->institutionId, $member->member_staff_id, $this->month, $this->year);
                if (!$contribution) {
                    $MEMBERS[] = $member;
                }
            }

            if (count($MEMBERS) == 0) {
                return ['response' => [], 'code' => 200];
            }

            return ['response' => $MEMBERS, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function exportMemberContributions()
    {
        try {

            $contributions = $this->_contribution->_getsWithMemberId($this->institutionId, $this->staffId);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $type = 'xlsx';

            $sheet->getStyle('A1:H1')->getFont()->setBold(true);
            $sheet->setCellValue('A1', 'MONTH');
            $sheet->setCellValue('B1', 'YEAR');
            $sheet->setCellValue('C1', 'STAFF ID');
            $sheet->setCellValue('D1', 'NAME');
            $sheet->setCellValue('E1', 'PHONE');
            $sheet->setCellValue('F1', 'AMOUNT (GHS)');
            $sheet->setCellValue('G1', 'DATE');

            $row = 2;
            foreach ($contributions as $contribution) {
                $sheet->setCellValue('A' . $row, $contribution->month);
                $sheet->setCellValue('B' . $row, $contribution->year);
                $sheet->setCellValue('C' . $row, $contribution->member_staff_id);
                $sheet->setCellValue('D' . $row, strtoupper($contribution->member_name));
                $sheet->setCellValueExplicit('E' . $row, $contribution->member_phone, DataType::TYPE_STRING);
                $sheet->setCellValue('F' . $row, number_format((float)$contribution->amount, 2, '.', ''));
                $sheet->setCellValueExplicit('G' . $row, $contribution->date, DataType::TYPE_STRING);
                $row++;

                $sheet->setCellValue("E$row", "Total")->getStyle("E" . (count($contributions) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("F$row", "=SUM(F2:F" . ($row - 1) . ")")->getStyle("F" . (count($contributions) + 2))->getFont()->setBold(true);
            }

            $fileName = 'contributions.' . $type;
            $writer = new Xlsx($spreadsheet);

            $writer->save(public_path() . '/files/' . $fileName);
            $headers = [
                'Content-Type: application/vnd.ms-excel',
                'Content-Transfer-Encoding: Binary',
                'Content-Disposition: attachment; filename=' . $fileName

            ];

            $file = public_path() . "/files/" . $fileName;

            $response = [
                'file' => $file,
                'filename' => $fileName,
                'headers' => $headers
            ];

            return $response;
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
}
