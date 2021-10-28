<?php

namespace App\Logic;

use App\Interfaces\ExpenseInterface;
use App\Models\Expense;
use App\Models\Member;
use App\Validations\ExpenseModelValidation;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use stdClass;

class ExpenseLogic implements ExpenseInterface
{
    private $_expense;
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

    public function __construct(Expense $expense, Member $member)
    {
        $this->_expense = $expense;
        $this->_member = $member;
    }

    public function addExpense()
    {
        $this->_validation = new ExpenseModelValidation($this);
        $validation = $this->_validation->__validateExpense();
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

            $this->_expense->_save($payload);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Expense saved successfully'
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function updateExpense()
    {
        $this->_validation = new ExpenseModelValidation($this);
        $validation = $this->_validation->__validateExpense();
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

            $this->_expense->_update($this->id, $payload);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Expense updated successfully',
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getExpenses()
    {
        try {
            $expenses = $this->_expense->_gets($this->institutionId, $this->month, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'expenses' => $expenses->isNotEmpty() ? $expenses : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getExpense()
    {
        try {
            $expense = $this->_expense->_get($this->id);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'expense' => $expense ? $expense : new stdClass()
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getExpensesSummary()
    {
        try {
            $summary = $this->_expense->_getSummary($this->institutionId);

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

    public function getOfficeExpenses()
    {
        try {
            $expenses = $this->_expense->_getOfficeExpenses($this->institutionId, $this->month, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'expenses' => $expenses->isNotEmpty() ? $expenses : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getMembersExpenses()
    {
        try {
            $expenses = $this->_expense->_getMembersExpenses($this->institutionId, $this->month, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'expenses' => $expenses->isNotEmpty() ? $expenses : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getMemberExpenses()
    {
        try {
            $expenses = $this->_expense->_getMemberExpenses($this->institutionId, $this->staffId, $this->month, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'expenses' => $expenses->isNotEmpty() ? $expenses : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getTypeExpenses()
    {
        try {
            $expenses = $this->_expense->_getsWithType($this->institutionId, $this->typeId, $this->month, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'expenses' => $expenses->isNotEmpty() ? $expenses : []
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function uploadExpenses()
    {
        $this->_validation = new ExpenseModelValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateExpensesUpload();
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

            $this->_expense->_save($payload);

            $response = [
                'success' => true,
                'description' => [
                    'message' => count($payload) . ' Expenses uploaded successfully'
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function deleteExpense()
    {
        try {
            $this->_expense->_delete($this->id);

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

    public function calculateYearlyTotalExpense()
    {
        try {
            $sum = $this->_expense->_calculateYearlyTotal($this->institutionId, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'expenses' => $sum
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function exportAllExpenses()
    {
        try {
            $expenses = $this->_expense->_gets($this->institutionId, $this->month, $this->year);

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
            foreach ($expenses as $expense) {
                $sheet->setCellValue('A' . $row, $expense->month);
                $sheet->setCellValue('B' . $row, $expense->year);
                $sheet->setCellValue('C' . $row, $expense->type);
                $sheet->setCellValueExplicit('D' . $row, $expense->member_staff_id, DataType::TYPE_STRING);
                $sheet->setCellValue('E' . $row, number_format((float)$expense->amount, 2, '.', ''));
                $sheet->setCellValue('F' . $row, $expense->description);
                $sheet->setCellValue('G' . $row, $expense->added_by);
                $sheet->setCellValue('H' . $row, $expense->updated_by);
                $sheet->setCellValueExplicit('I' . $row, $expense->date, DataType::TYPE_STRING);
                $row++;

                $sheet->setCellValue("D$row", "Total:")->getStyle("D" . (count($expenses) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("E$row", "=SUM(E2:E" . ($row - 1) . ")")->getStyle("E" . (count($expenses) + 2))->getFont()->setBold(true);
            }

            $fileName = 'all_expenses_' . $this->month . '_' . $this->year . '.' . $type;
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

    public function exportMembersExpenses()
    {
        try {
            $expenses = $this->_expense->_getMembersExpenses($this->institutionId, $this->month, $this->year);

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
            foreach ($expenses as $expense) {
                $sheet->setCellValue('A' . $row, $expense->month);
                $sheet->setCellValue('B' . $row, $expense->year);
                $sheet->setCellValue('C' . $row, $expense->type);
                $sheet->setCellValueExplicit('D' . $row, $expense->member_staff_id, DataType::TYPE_STRING);
                $sheet->setCellValue('E' . $row, strtoupper($expense->member_name));
                $sheet->setCellValueExplicit('F' . $row, $expense->member_phone, DataType::TYPE_STRING);
                $sheet->setCellValue('G' . $row, number_format((float)$expense->amount, 2, '.', ''));
                $sheet->setCellValue('H' . $row, $expense->description);
                $sheet->setCellValue('I' . $row, $expense->added_by);
                $sheet->setCellValue('J' . $row, $expense->updated_by);
                $sheet->setCellValueExplicit('K' . $row, $expense->date, DataType::TYPE_STRING);
                $row++;

                $sheet->setCellValue("F$row", "Total:")->getStyle("F" . (count($expenses) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("G$row", "=SUM(G2:G" . ($row - 1) . ")")->getStyle("G" . (count($expenses) + 2))->getFont()->setBold(true);
            }

            $fileName = 'members_expenses_' . $this->month . '_' . $this->year . '.' . $type;
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

    public function exportOfficeExpenses()
    {
        try {
            $expenses = $this->_expense->_getOfficeExpenses($this->institutionId, $this->month, $this->year);

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
            foreach ($expenses as $expense) {
                $sheet->setCellValue('A' . $row, $expense->month);
                $sheet->setCellValue('B' . $row, $expense->year);
                $sheet->setCellValue('C' . $row, $expense->type);
                $sheet->setCellValue('D' . $row, number_format((float)$expense->amount, 2, '.', ''));
                $sheet->setCellValue('E' . $row, $expense->description);
                $sheet->setCellValue('F' . $row, $expense->added_by);
                $sheet->setCellValue('G' . $row, $expense->updated_by);
                $sheet->setCellValueExplicit('H' . $row, $expense->date, DataType::TYPE_STRING);
                $row++;

                $sheet->setCellValue("C$row", "Total:")->getStyle("C" . (count($expenses) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("D$row", "=SUM(D2:D" . ($row - 1) . ")")->getStyle("D" . (count($expenses) + 2))->getFont()->setBold(true);
            }

            $fileName = 'office_expenses_' . $this->month . '_' . $this->year . '.' . $type;
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

    public function exportMemberExpenses()
    {
        try {
            $expenses = $this->_expense->_getMemberExpenses($this->institutionId, $this->staffId, $this->month, $this->year);

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
            foreach ($expenses as $expense) {
                $sheet->setCellValue('A' . $row, $expense->month);
                $sheet->setCellValue('B' . $row, $expense->year);
                $sheet->setCellValue('C' . $row, $expense->type);
                $sheet->setCellValue('D' . $row, number_format((float)$expense->amount, 2, '.', ''));
                $sheet->setCellValue('E' . $row, $expense->description);
                $sheet->setCellValue('F' . $row, $expense->added_by);
                $sheet->setCellValue('G' . $row, $expense->updated_by);
                $sheet->setCellValueExplicit('H' . $row, $expense->date, DataType::TYPE_STRING);
                $row++;

                $sheet->setCellValue("C$row", "Total:")->getStyle("C" . (count($expenses) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("D$row", "=SUM(D2:D" . ($row - 1) . ")")->getStyle("D" . (count($expenses) + 2))->getFont()->setBold(true);
            }

            $fileName = $this->staffId . '_expenses_' . $this->month . '_' . $this->year . '.' . $type;
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
