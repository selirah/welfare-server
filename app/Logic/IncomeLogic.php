<?php

namespace App\Logic;

use App\Interfaces\IncomeInterface;
use App\Models\Income;
use App\Models\Member;
use App\Validations\IncomeModelValidation;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use stdClass;

class IncomeLogic implements IncomeInterface
{
    private $_income;
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

    public function __construct(Income $income, Member $member)
    {
        $this->_income = $income;
        $this->_member = $member;
    }

    public function addIncome()
    {
        $this->_validation = new IncomeModelValidation($this);
        $validation = $this->_validation->__validateIncome();
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

            $this->_income->_save($payload);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Income saved successfully'
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function updateIncome()
    {
        $this->_validation = new IncomeModelValidation($this);
        $validation = $this->_validation->__validateIncome();
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

            $this->_income->_update($this->id, $payload);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Income updated successfully',
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getIncomes()
    {
        try {
            $incomes = $this->_income->_gets($this->institutionId, $this->month, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'incomes' => $incomes->isNotEmpty() ? $incomes : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getIncome()
    {
        try {
            $income = $this->_income->_get($this->id);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'income' => $income ? $income : new stdClass()
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getIncomesSummary()
    {
        try {
            $summary = $this->_income->_getSummary($this->institutionId);

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

    public function getOfficeIncomes()
    {
        try {
            $incomes = $this->_income->_getOfficeIncomes($this->institutionId, $this->month, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'incomes' => $incomes->isNotEmpty() ? $incomes : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getMembersIncomes()
    {
        try {
            $incomes = $this->_income->_getMembersIncomes($this->institutionId, $this->month, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'incomes' => $incomes->isNotEmpty() ? $incomes : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getMemberIncomes()
    {
        try {
            $incomes = $this->_income->_getMemberIncomes($this->institutionId, $this->staffId, $this->month, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'incomes' => $incomes->isNotEmpty() ? $incomes : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getTypeIncomes()
    {
        try {
            $incomes = $this->_income->_getsWithType($this->institutionId, $this->typeId, $this->month, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'incomes' => $incomes->isNotEmpty() ? $incomes : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function uploadIncomes()
    {
        $this->_validation = new IncomeModelValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateIncomesUpload();
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

            $this->_income->_save($payload);

            $response = [
                'success' => true,
                'description' => [
                    'message' => count($payload) . ' Incomes uploaded successfully'
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function deleteIncome()
    {
        try {
            $this->_income->_delete($this->id);

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

    public function calculateYearlyTotalIncome()
    {
        try {
            $sum = $this->_income->_calculateYearlyTotal($this->institutionId, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'incomes' => $sum
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function exportAllIncomes()
    {
        try {
            $incomes = $this->_income->_gets($this->institutionId, $this->month, $this->year);

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
            foreach ($incomes as $income) {
                $sheet->setCellValue('A' . $row, $income->month);
                $sheet->setCellValue('B' . $row, $income->year);
                $sheet->setCellValue('C' . $row, $income->type);
                $sheet->setCellValueExplicit('D' . $row, $income->member_staff_id, DataType::TYPE_STRING);
                $sheet->setCellValue('E' . $row, number_format((float)$income->amount, 2, '.', ''));
                $sheet->setCellValue('F' . $row, $income->description);
                $sheet->setCellValue('G' . $row, $income->added_by);
                $sheet->setCellValue('H' . $row, $income->updated_by);
                $sheet->setCellValueExplicit('I' . $row, $income->date, DataType::TYPE_STRING);
                $row++;

                $sheet->setCellValue("D$row", "Total:")->getStyle("D" . (count($incomes) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("E$row", "=SUM(E2:E" . ($row - 1) . ")")->getStyle("E" . (count($incomes) + 2))->getFont()->setBold(true);
            }

            $fileName = 'all_incomes_' . $this->month . '_' . $this->year . '.' . $type;
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

    public function exportMembersIncomes()
    {
        try {
            $incomes = $this->_income->_getMembersIncomes($this->institutionId, $this->month, $this->year);

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
            foreach ($incomes as $income) {
                $sheet->setCellValue('A' . $row, $income->month);
                $sheet->setCellValue('B' . $row, $income->year);
                $sheet->setCellValue('C' . $row, $income->type);
                $sheet->setCellValueExplicit('D' . $row, $income->member_staff_id, DataType::TYPE_STRING);
                $sheet->setCellValue('E' . $row, strtoupper($income->member_name));
                $sheet->setCellValueExplicit('F' . $row, $income->member_phone, DataType::TYPE_STRING);
                $sheet->setCellValue('G' . $row, number_format((float)$income->amount, 2, '.', ''));
                $sheet->setCellValue('H' . $row, $income->description);
                $sheet->setCellValue('I' . $row, $income->added_by);
                $sheet->setCellValue('J' . $row, $income->updated_by);
                $sheet->setCellValueExplicit('K' . $row, $income->date, DataType::TYPE_STRING);
                $row++;

                $sheet->setCellValue("F$row", "Total:")->getStyle("F" . (count($incomes) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("G$row", "=SUM(G2:G" . ($row - 1) . ")")->getStyle("G" . (count($incomes) + 2))->getFont()->setBold(true);
            }

            $fileName = 'members_incomes_' . $this->month . '_' . $this->year . '.' . $type;
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

    public function exportOfficeIncomes()
    {
        try {
            $incomes = $this->_income->_getOfficeIncomes($this->institutionId, $this->month, $this->year);

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
            foreach ($incomes as $income) {
                $sheet->setCellValue('A' . $row, $income->month);
                $sheet->setCellValue('B' . $row, $income->year);
                $sheet->setCellValue('C' . $row, $income->type);
                $sheet->setCellValue('D' . $row, number_format((float)$income->amount, 2, '.', ''));
                $sheet->setCellValue('E' . $row, $income->description);
                $sheet->setCellValue('F' . $row, $income->added_by);
                $sheet->setCellValue('G' . $row, $income->updated_by);
                $sheet->setCellValueExplicit('H' . $row, $income->date, DataType::TYPE_STRING);
                $row++;

                $sheet->setCellValue("C$row", "Total:")->getStyle("C" . (count($incomes) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("D$row", "=SUM(D2:D" . ($row - 1) . ")")->getStyle("D" . (count($incomes) + 2))->getFont()->setBold(true);
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

    public function exportMemberIncomes()
    {
        try {
            $incomes = $this->_income->_getMemberIncomes($this->institutionId, $this->staffId, $this->month, $this->year);

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
            foreach ($incomes as $income) {
                $sheet->setCellValue('A' . $row, $income->month);
                $sheet->setCellValue('B' . $row, $income->year);
                $sheet->setCellValue('C' . $row, $income->type);
                $sheet->setCellValue('D' . $row, number_format((float)$income->amount, 2, '.', ''));
                $sheet->setCellValue('E' . $row, $income->description);
                $sheet->setCellValue('F' . $row, $income->added_by);
                $sheet->setCellValue('G' . $row, $income->updated_by);
                $sheet->setCellValueExplicit('H' . $row, $income->date, DataType::TYPE_STRING);
                $row++;

                $sheet->setCellValue("C$row", "Total:")->getStyle("C" . (count($incomes) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("D$row", "=SUM(D2:D" . ($row - 1) . ")")->getStyle("D" . (count($incomes) + 2))->getFont()->setBold(true);
            }

            $fileName = $this->staffId . '_incomes_' . $this->month . '_' . $this->year . '.' . $type;
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
