<?php

namespace App\Logic;

use App\Interfaces\AdminInterface;
use App\Models\Contribution;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Institution;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AdminLogic implements AdminInterface
{
    private $_institution;
    private $_user;
    private $_contribution;
    private $_loan;
    private $_donation;
    private $_expense;
    private $_income;
    private $_member;

    public $month;
    public $year;
    public $userId;
    public $adminId;
    public $institutionId;

    public function __construct(Institution $institution, User $user, Contribution $contribution, Loan $loan, Donation $donation, Member $member, Expense $expense, Income $income)
    {
        $this->_institution = $institution;
        $this->_user = $user;
        $this->_contribution = $contribution;
        $this->_loan = $loan;
        $this->_donation = $donation;
        $this->_expense = $expense;
        $this->_income = $income;
        $this->_member = $member;
    }

    public function getClients()
    {
        try {
            // fetch institutions
            $institutions = $this->_institution->_gets();

            if ($institutions->isEmpty()) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'No client has registered on this system',
                        'clients' => []
                    ]
                ];
                return ['response' => $response, 'code' => 400];
            }

            $clients = [];

            foreach ($institutions as $institution) {
                $totalMembers = $this->_member->_getTotal($institution->id);
                $totalContributions = $this->_contribution->_calculateYearlyTotal($institution->id, $this->year);
                $totalLoans = $this->_loan->_calculateYearlyTotal($institution->id, $this->year);
                $totalDonations = $this->_donation->_calculateYearlyTotal($institution->id, $this->year);
                $totalExpenses = $this->_expense->_calculateYearlyTotal($institution->id, $this->year);
                $totalIncomes = $this->_income->_calculateYearlyTotal($institution->id, $this->year);

                $clients[] = [
                    'id' => $institution->id,
                    'inst_name' => $institution->name,
                    'user_id' => $institution->user_id,
                    'location' => $institution->location,
                    'phone' => $institution->phone,
                    'created_at' => $institution->created_at,
                    'members' => $totalMembers,
                    'contributions' => $totalContributions,
                    'loans' => $totalLoans,
                    'donations' => $totalDonations,
                    'expenses' => $totalExpenses,
                    'incomes' => $totalIncomes,
                ];
            }

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'clients' => $clients
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function impersonateClient(Request $request)
    {
        try {

            $user = Auth::guard('web')->loginUsingId($this->userId);
            Auth::setUser($user);
            $auth = $request->user();

            $tokenResult = $auth->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->expires_at = date("Y-m-d H:i:s", strtotime('+1 hours'));
            $token->save;

            // get user institution, if they have one set
            $institution = $this->_institution->_get($auth->institution_id);

            // prepare auth data
            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Logged in successfully',
                    'auth' => [
                        'institution_id' => $user->institution_id,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'name' => $user->name,
                        'role' => $user->role,
                        'avatar' => $user->avatar,
                        'token' => 'Bearer ' . $tokenResult->accessToken,
                        'token_exp' => strtotime($tokenResult->token->expires_at),
                    ],
                    'institution' => ($institution) ? $institution : null,
                    'logo' => ($institution) ? $institution->logo : null,
                    'admin_id' => $this->adminId
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function impersonateAdmin(Request $request)
    {
        try {

            $user = Auth::guard('web')->loginUsingId($this->adminId);
            Auth::setUser($user);
            $auth = $request->user();

            $tokenResult = $auth->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->expires_at = date("Y-m-d H:i:s", strtotime('+1 hours'));
            $token->save;

            // get user institution, if they have one set
            $institution = $this->_institution->_get($auth->institution_id);

            // prepare auth data
            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Logged in successfully',
                    'auth' => [
                        'institution_id' => $user->institution_id,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'name' => $user->name,
                        'role' => $user->role,
                        'avatar' => $user->avatar,
                        'token' => 'Bearer ' . $tokenResult->accessToken,
                        'token_exp' => strtotime($tokenResult->token->expires_at),
                    ],
                    'institution' => ($institution) ? $institution : null,
                    'logo' => ($institution) ? $institution->logo : null
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function exportClients()
    {
        try {
            $institutions = $this->_institution->_gets();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $type = 'xlsx';

            $sheet->getStyle('A1:J1')->getFont()->setBold(true);
            $sheet->setCellValue('A1', 'ORGANIZATION');
            $sheet->setCellValue('B1', 'LOCATION');
            $sheet->setCellValue('C1', 'PHONE');
            $sheet->setCellValue('D1', 'NUMBER OF MEMBERS');
            $sheet->setCellValue('E1', 'TOTAL CONTRIBUTIONS (' . $this->year . ')');
            $sheet->setCellValue('F1', 'TOTAL LOANS (' . $this->year . ')');
            $sheet->setCellValue('G1', 'TOTAL DONATIONS (' . $this->year . ')');
            $sheet->setCellValue('H1', 'TOTAL EXPENSES (' . $this->year . ')');
            $sheet->setCellValue('I1', 'TOTAL INCOME (' . $this->year . ')');
            $sheet->setCellValue('J1', 'DATE REGISTERED');

            $row = 2;
            foreach ($institutions as $institution) {
                $totalMembers = $this->_member->_getTotal($institution->id);
                $totalContributions = $this->_contribution->_calculateYearlyTotal($institution->id, $this->year);
                $totalLoans = $this->_loan->_calculateYearlyTotal($institution->id, $this->year);
                $totalDonations = $this->_donation->_calculateYearlyTotal($institution->id, $this->year);
                $totalExpenses = $this->_expense->_calculateYearlyTotal($institution->id, $this->year);
                $totalIncomes = $this->_income->_calculateYearlyTotal($institution->id, $this->year);

                $sheet->setCellValue('A' . $row, $institution->name);
                $sheet->setCellValue('B' . $row, $institution->location);
                $sheet->setCellValueExplicit('C' . $row, $institution->phone, DataType::TYPE_STRING);
                $sheet->setCellValue('D' . $row, $totalMembers);
                $sheet->setCellValue('E' . $row, number_format((float)$totalContributions, 2, '.', ''));
                $sheet->setCellValue('F' . $row, number_format((float)$totalLoans, 2, '.', ''));
                $sheet->setCellValue('G' . $row, number_format((float)$totalDonations, 2, '.', ''));
                $sheet->setCellValue('H' . $row, number_format((float)$totalExpenses, 2, '.', ''));
                $sheet->setCellValue('I' . $row, number_format((float)$totalIncomes, 2, '.', ''));
                $sheet->setCellValueExplicit('J' . $row, $institution->created_at, DataType::TYPE_STRING);
                $row++;

                $sheet->setCellValue("C$row", "Total:")->getStyle("C" . (count($institutions) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("D$row", "=SUM(D2:D" . ($row - 1) . ")")->getStyle("D" . (count($institutions) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("E$row", "=SUM(E2:E" . ($row - 1) . ")")->getStyle("E" . (count($institutions) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("F$row", "=SUM(F2:F" . ($row - 1) . ")")->getStyle("F" . (count($institutions) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("G$row", "=SUM(G2:G" . ($row - 1) . ")")->getStyle("G" . (count($institutions) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("H$row", "=SUM(H2:H" . ($row - 1) . ")")->getStyle("H" . (count($institutions) + 2))->getFont()->setBold(true);
                $sheet->setCellValue("I$row", "=SUM(I2:I" . ($row - 1) . ")")->getStyle("I" . (count($institutions) + 2))->getFont()->setBold(true);
            }

            $fileName = 'all_clients.' . $type;
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

    public function getTotals()
    {
        try {
            $members = $this->_member->_getTotal($this->institutionId);
            $contributions = $this->_contribution->_calculateYearlyTotal($this->institutionId, $this->year);
            $loans = $this->_loan->_calculateYearlyTotal($this->institutionId, $this->year);
            $donations = $this->_donation->_calculateYearlyTotal($this->institutionId, $this->year);
            $expenses = $this->_expense->_calculateYearlyTotal($this->institutionId, $this->year);
            $incomes = $this->_income->_calculateYearlyTotal($this->institutionId, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'totals' => [
                        'members' => $members,
                        'contributions' => $contributions,
                        'loans' => $loans,
                        'donations' => $donations,
                        'expenses' => $expenses,
                        'incomes' => $incomes
                    ],
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getYearlySummary()
    {
        try {
            $contributions = $this->_contribution->_getYearlySummary($this->institutionId, $this->year);
            $loans = $this->_loan->_getYearlySummary($this->institutionId, $this->year);
            $donations = $this->_donation->_getYearlySummary($this->institutionId, $this->year);
            $expenses = $this->_expense->_getYearlySummary($this->institutionId, $this->year);
            $incomes = $this->_income->_getYearlySummary($this->institutionId, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'yearly_summary' => [
                        'contributions' => $contributions,
                        'loans' => $loans,
                        'donations' => $donations,
                        'expenses' => $expenses,
                        'incomes' => $incomes
                    ],
                ]
            ];
            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
}
