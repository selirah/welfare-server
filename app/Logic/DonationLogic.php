<?php

namespace App\Logic;

use App\Interfaces\DonationInterface;
use App\Models\Donation;
use App\Models\DonationSetting;
use App\Models\Member;
use App\Validations\DonationModelValidation;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use stdClass;

class DonationLogic implements DonationInterface
{
    private $_donation;
    private $_member;
    private $_validation;

    public $id;
    public $staffId;
    public $institutionId;
    public $typeId;
    public $month;
    public $year;
    public $amount;
    public $description;
    public $userName;
    public $hasFile;
    public $tmpPath;
    public $excel;
    public $extension;
    public $size;
    public $sheet;
    public $startRow;

    public function __construct(Donation $donation, Member $member)
    {
        $this->_donation = $donation;
        $this->_member = $member;
    }

    public function addDonation()
    {
        $this->_validation = new DonationModelValidation($this);
        $validation = $this->_validation->__validateDonation();
        if ($validation !== true) {
            return $validation;
        }

        try {
            $payload = [
                'institution_id' => $this->institutionId,
                'type_id' => $this->typeId,
                'member_staff_id' => $this->staffId,
                'amount' => $this->amount,
                'description' => $this->description,
                'month' => $this->month,
                'year' => $this->year,
                'date' => date('Y-m-d'),
                'added_by' => $this->userName,
                'updated_by' => $this->userName,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $this->_donation->_save($payload);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Donation saved successfully'
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function updateDonation()
    {
        $this->_validation = new DonationModelValidation($this);
        $validation = $this->_validation->__validateDonation();
        if ($validation !== true) {
            return $validation;
        }

        try {
            $payload = [
                'type_id' => $this->typeId,
                'member_staff_id' => $this->staffId,
                'amount' => $this->amount,
                'description' => $this->description,
                'month' => $this->month,
                'year' => $this->year,
                'date' => date('Y-m-d'),
                'updated_by' => $this->userName,
                'updated_at' => Carbon::now(),
            ];

            $this->_donation->_update($this->id, $payload);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Donation updated successfully',
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getDonations()
    {
        try {
            $donations = $this->_donation->_gets($this->institutionId, $this->month, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'donations' => $donations->isNotEmpty() ? $donations : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getDonation()
    {
        try {
            $donation = $this->_donation->_get($this->id);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'donation' => $donation ? $donation : new stdClass()
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getDonationsSummary()
    {
        try {
            $summary = $this->_donation->_getSummary($this->institutionId);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'summary' => $summary->isNotEmpty() ? $summary : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getOfficeDonations()
    {
        try {
            $donations = $this->_donation->_getOfficeDonations($this->institutionId, $this->month, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'donations' => $donations->isNotEmpty() ? $donations : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getMembersDonations()
    {
        try {
            $donations = $this->_donation->_getMembersDonations($this->institutionId, $this->month, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'donations' => $donations->isNotEmpty() ? $donations : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getMemberDonations()
    {
        try {
            $donations = $this->_donation->_getMemberDonations($this->institutionId, $this->staffId, $this->month, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'donations' => $donations->isNotEmpty() ? $donations : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getTypeDonations()
    {
        try {
            $donations = $this->_donation->_getsWithType($this->institutionId, $this->typeId, $this->month, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'donations' => $donations->isNotEmpty() ? $donations : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function uploadDonations()
    {
        $this->_validation = new donationModelValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateDonationsUpload();
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

            $payload = [];
            foreach ($cleanData as $c) {
                if ($c['A'] != null || $c['A'] == '' && $c['B'] != null || $c['B']) {
                    // check if member already exist
                    if ($c['A'] !== "office") {
                        if (!$this->_member->_getWithStaffId($this->institutionId, $c['A'])) {
                            $response = [
                                'success' => false,
                                'description' => [
                                    'message' => 'Staff ID ' . $c['A'] . ' does not exist'
                                ]
                            ];
                            return ['response' => $response, 'code' => 400];
                        }
                    }

                    $payload[] = [
                        'institution_id' => $this->institutionId,
                        'type_id' => $this->typeId,
                        'member_staff_id' => $c['A'],
                        'amount' => $c['B'],
                        'description' => $c['C'],
                        'month' => $this->month,
                        'year' => $this->year,
                        'date' => date('Y-m-d'),
                        'added_by' => $this->userName,
                        'updated_by' => $this->userName,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
            }

            $this->_donation->_save($payload);

            $response = [
                'success' => true,
                'description' => [
                    'message' => count($payload) . ' donations uploaded successfully'
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function deleteDonation()
    {
        try {
            $this->_donation->_delete($this->id);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful'
                ]
            ];

            return ['response' => $response, 'code' => 204];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function calculateYearlyTotalDonation()
    {
        try {
            $sum = $this->_donation->_calculateYearlyTotal($this->institutionId, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'donations' => $sum
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function exportAllDonations()
    {
        try {
            $donations = $this->_donation->_gets($this->institutionId, $this->month, $this->year);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $type = 'xlsx';

            $sheet->getStyle('A1:I1')->getFont()->setBold(true);
            $sheet->setCellValue('A1', 'MONTH');
            $sheet->setCellValue('B1', 'YEAR');
            $sheet->setCellValue('C1', 'TYPE');
            $sheet->setCellValue('D1', 'STAFF ID');
            $sheet->setCellValue('E1', 'AMOUNT (GHS)');
            $sheet->setCellValue('F1', 'DESCRIPTION');
            $sheet->setCellValue('G1', 'ENTERED BY');
            $sheet->setCellValue('H1', 'MODIFIED BY');
            $sheet->setCellValue('I1', 'DATE');

            $row = 2;
            foreach ($donations as $donation) {
                $sheet->setCellValue('A' . $row, $donation->month);
                $sheet->setCellValue('B' . $row, $donation->year);
                $sheet->setCellValue('C' . $row, $donation->type);
                $sheet->setCellValueExplicit('D' . $row, $donation->member_staff_id, DataType::TYPE_STRING);
                $sheet->setCellValue('E' . $row, number_format((float)$donation->amount, 2, '.', ''));
                $sheet->setCellValue('F' . $row, $donation->description);
                $sheet->setCellValue('G' . $row, $donation->added_by);
                $sheet->setCellValue('H' . $row, $donation->updated_by);
                $sheet->setCellValueExplicit('I' . $row, $donation->date, DataType::TYPE_STRING);
                $row++;

                $sheet->setCellValue("D$row", "Total:")->getStyle("D" . (count($donations) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("E$row", "=SUM(E2:E" . ($row - 1) . ")")->getStyle("E" . (count($donations) + 2))->getFont()->setBold(true);
            }

            $fileName = 'all_donations_' . $this->month . '_' . $this->year . '.' . $type;
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

    public function exportMembersDonations()
    {
        try {
            $donations = $this->_donation->_getMembersDonations($this->institutionId, $this->month, $this->year);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $type = 'xlsx';

            $sheet->getStyle('A1:K1')->getFont()->setBold(true);
            $sheet->setCellValue('A1', 'MONTH');
            $sheet->setCellValue('B1', 'YEAR');
            $sheet->setCellValue('C1', 'TYPE');
            $sheet->setCellValue('D1', 'STAFF ID');
            $sheet->setCellValue('E1', 'NAME');
            $sheet->setCellValue('F1', 'PHONE');
            $sheet->setCellValue('G1', 'AMOUNT (GHS)');
            $sheet->setCellValue('H1', 'DESCRIPTION');
            $sheet->setCellValue('I1', 'ENTERED BY');
            $sheet->setCellValue('J1', 'MODIFIED BY');
            $sheet->setCellValue('K1', 'DATE');

            $row = 2;
            foreach ($donations as $donation) {
                $sheet->setCellValue('A' . $row, $donation->month);
                $sheet->setCellValue('B' . $row, $donation->year);
                $sheet->setCellValue('C' . $row, $donation->type);
                $sheet->setCellValueExplicit('D' . $row, $donation->member_staff_id, DataType::TYPE_STRING);
                $sheet->setCellValue('E' . $row, strtoupper($donation->member_name));
                $sheet->setCellValueExplicit('F' . $row, $donation->member_phone, DataType::TYPE_STRING);
                $sheet->setCellValue('G' . $row, number_format((float)$donation->amount, 2, '.', ''));
                $sheet->setCellValue('H' . $row, $donation->description);
                $sheet->setCellValue('I' . $row, $donation->added_by);
                $sheet->setCellValue('J' . $row, $donation->updated_by);
                $sheet->setCellValueExplicit('K' . $row, $donation->date, DataType::TYPE_STRING);
                $row++;

                $sheet->setCellValue("F$row", "Total:")->getStyle("F" . (count($donations) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("G$row", "=SUM(G2:G" . ($row - 1) . ")")->getStyle("G" . (count($donations) + 2))->getFont()->setBold(true);
            }

            $fileName = 'members_donations_' . $this->month . '_' . $this->year . '.' . $type;
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

    public function exportOfficeDonations()
    {
        try {
            $donations = $this->_donation->_getOfficeDonations($this->institutionId, $this->month, $this->year);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $type = 'xlsx';

            $sheet->getStyle('A1:H1')->getFont()->setBold(true);
            $sheet->setCellValue('A1', 'MONTH');
            $sheet->setCellValue('B1', 'YEAR');
            $sheet->setCellValue('C1', 'TYPE');
            $sheet->setCellValue('D1', 'AMOUNT (GHS)');
            $sheet->setCellValue('E1', 'DESCRIPTION');
            $sheet->setCellValue('F1', 'ENTERED BY');
            $sheet->setCellValue('G1', 'MODIFIED BY');
            $sheet->setCellValue('H1', 'DATE');

            $row = 2;
            foreach ($donations as $donation) {
                $sheet->setCellValue('A' . $row, $donation->month);
                $sheet->setCellValue('B' . $row, $donation->year);
                $sheet->setCellValue('C' . $row, $donation->type);
                $sheet->setCellValue('D' . $row, number_format((float)$donation->amount, 2, '.', ''));
                $sheet->setCellValue('E' . $row, $donation->description);
                $sheet->setCellValue('F' . $row, $donation->added_by);
                $sheet->setCellValue('G' . $row, $donation->updated_by);
                $sheet->setCellValueExplicit('H' . $row, $donation->date, DataType::TYPE_STRING);
                $row++;

                $sheet->setCellValue("C$row", "Total:")->getStyle("C" . (count($donations) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("D$row", "=SUM(D2:D" . ($row - 1) . ")")->getStyle("D" . (count($donations) + 2))->getFont()->setBold(true);
            }

            $fileName = 'office_revenue_' . $this->month . '_' . $this->year . '.' . $type;
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

    public function exportMemberDonations()
    {
        try {
            $donations = $this->_donation->_getMemberDonations($this->institutionId, $this->staffId, $this->month, $this->year);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $type = 'xlsx';

            $sheet->getStyle('A1:H1')->getFont()->setBold(true);
            $sheet->setCellValue('A1', 'MONTH');
            $sheet->setCellValue('B1', 'YEAR');
            $sheet->setCellValue('C1', 'TYPE');
            $sheet->setCellValue('D1', 'AMOUNT (GHS)');
            $sheet->setCellValue('E1', 'DESCRIPTION');
            $sheet->setCellValue('F1', 'ENTERED BY');
            $sheet->setCellValue('G1', 'MODIFIED BY');
            $sheet->setCellValue('H1', 'DATE');

            $row = 2;
            foreach ($donations as $donation) {
                $sheet->setCellValue('A' . $row, $donation->month);
                $sheet->setCellValue('B' . $row, $donation->year);
                $sheet->setCellValue('C' . $row, $donation->type);
                $sheet->setCellValue('D' . $row, number_format((float)$donation->amount, 2, '.', ''));
                $sheet->setCellValue('E' . $row, $donation->description);
                $sheet->setCellValue('F' . $row, $donation->added_by);
                $sheet->setCellValue('G' . $row, $donation->updated_by);
                $sheet->setCellValueExplicit('H' . $row, $donation->date, DataType::TYPE_STRING);
                $row++;

                $sheet->setCellValue("C$row", "Total:")->getStyle("C" . (count($donations) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("D$row", "=SUM(D2:D" . ($row - 1) . ")")->getStyle("D" . (count($donations) + 2))->getFont()->setBold(true);
            }

            $fileName = $this->staffId . '_donations_' . $this->month . '_' . $this->year . '.' . $type;
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
