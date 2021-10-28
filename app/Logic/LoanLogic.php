<?php

namespace App\Logic;


use App\Interfaces\LoanInterface;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\LoanSetting;
use App\Models\Member;
use App\Models\User;
use App\Validations\LoanModelValidation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class LoanLogic implements LoanInterface
{
    private $_loan;
    private $_loanSetting;
    private $_member;
    private $_user;
    private $_loanPayment;
    private $_validation;

    public $userId;
    public $id;
    public $loanType;
    public $staffId;
    public $institutionId;
    public $month;
    public $year;
    public $amountLoaned;
    public $time;
    public $interest;
    public $password;
    public $hashedPassword;
    public $amountPaid;
    public $returnDate;

    public function __construct(Loan $loan, Member $member, User $user, LoanSetting $loanSetting, LoanPayment $loanPayment)
    {
        $this->_loan = $loan;
        $this->_member = $member;
        $this->_user = $user;
        $this->_loanSetting = $loanSetting;
        $this->_loanPayment = $loanPayment;
    }

    public function grantLoan()
    {
        $this->_validation = new LoanModelValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateLoan();
        if ($validation !== true) {
            return $validation;
        }

        try {
            // check if password matches that of the user granting the loan
            if (!Hash::check($this->password, $this->hashedPassword)) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'Password do not match'
                    ]
                ];
                return ['response' => $response, 'code' => 400];
            }

            $member = $this->_member->_getWithStaffId($this->institutionId, $this->staffId);
            if (!$member) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'This staff ID is invalid'
                    ]
                ];
                return ['response' => $response, 'code' => 400];
            }

            // check if the time in months specified is bounded by the min and max months of the loan type
            $loanType = $this->_loanSetting->_get($this->loanType);
            if (($this->time < $loanType->min_month) || ($this->time > $loanType->max_month)) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'The minimum and maximum months for this loan type is ' . $loanType->min_month . ' and ' . $loanType->max_month . ' months respectively'
                    ]
                ];
                return ['response' => $response, 'code' => 400];
            }


            // let's calculate the interest Simple Interest = Principal * rate(/100 if it's in percentage) * time(in years)
            $this->interest = ($this->amountLoaned * $loanType->rate * ($this->time / 12));


            // save loan to DB
            $payload = [
                'institution_id' => $this->institutionId,
                'member_staff_id' => $this->staffId,
                'loan_type' => $this->loanType,
                'month' => $this->month,
                'year' => $this->year,
                'date' => date('Y-m-d'),
                'amount_loaned' => $this->amountLoaned,
                'interest' => $this->interest,
                'amount_paid' => '0.00',
                'return_date' => date("Y-m-d", strtotime('+' . $this->time . ' months')),
                'added_by' => $this->userId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            $this->_loan->_save($payload);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Loan granted successfully'
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function updateLoan()
    {
        $this->_validation = new LoanModelValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateUpdate();
        if ($validation !== true) {
            return $validation;
        }

        try {
            // check if password matches that of the user granting the loan
            if (!Hash::check($this->password, $this->hashedPassword)) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'Password do not match'
                    ]
                ];
                return ['response' => $response, 'code' => 400];
            }

            $loan = $this->_loan->_get($this->id);

            if ($loan) {
                // save loan to DB
                $payload = [
                    'amount_paid' => $this->amountPaid + $loan->amount_paid,
                    'updated_at' => Carbon::now()
                ];
                $this->_loan->_update($this->id, $payload);
            }

            // save payment in loan payments table
            $payment = [
                'loan_id' => $this->id,
                'amount' => $this->amountPaid,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            $this->_loanPayment->_save($payment);

            // fetch loans
            $loans = $this->_loan->_gets($this->institutionId, $this->month, $this->year);

            $LOANS = [];
            foreach ($loans as $loan) {

                $total = $loan->amount_loaned + $loan->interest;
                $loanType = $this->_loanSetting->_get($loan->loan_type);

                $LOANS[] = [
                    'id' => $loan->id,
                    'institution_id' => $loan->institution_id,
                    'member_staff_id' => $loan->member_staff_id,
                    'month' => $loan->month,
                    'year' => $loan->year,
                    'date' => $loan->date,
                    'amount_loaned' => $loan->amount_loaned,
                    'amount_paid' => $loan->amount_paid,
                    'loan_type' => $loanType->type,
                    'rate' => $loanType->rate,
                    'interest' => $loan->interest,
                    'return_date' => $loan->return_date,
                    'added_by' => $this->_user->_getUser($loan->added_by)->name,
                    'created_at' => $loan->created_at,
                    'updated_at' => $loan->updated_at,
                    'member_name' => $loan->member_name,
                    'status' => ($total == $loan->amount_paid) ? 'Paid' : 'Owing',
                    'amount_owing' => ($loan->amount_paid - ($loan->amount_loaned + $loan->interest))
                ];
            }

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'Payment made successfully',
                    'loans' => $LOANS
                ]
            ];

            return ['response' => $response, 'code' => 201];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getLoanSummary()
    {
        try {
            // get loans summary
            $summary = $this->_loan->_getSummary($this->institutionId);

            if ($summary->isEmpty()) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'No records found',
                        'summary' => []
                    ]
                ];
                return ['response' => $response, 'code' => 404];
            }

            $SUMMARY = [];

            foreach ($summary as $s) {
                $SUMMARY[] = [
                    'month' => $s->month,
                    'year' => $s->year,
                    'total' => $s->total,
                    'amount_loaned' => $s->amount_loaned,
                    'amount_paid' => $s->amount_paid
                ];
            }

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'summary' => $SUMMARY
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getLoans()
    {
        try {

            // fetch loans
            $loans = $this->_loan->_gets($this->institutionId, $this->month, $this->year);

            if ($loans->isEmpty()) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'No records found'
                    ]
                ];
                return ['response' => $response, 'code' => 404];
            }

            $LOANS = [];
            foreach ($loans as $loan) {

                $total = $loan->amount_loaned + $loan->interest;
                $loanType = $this->_loanSetting->_get($loan->loan_type);
                $owing = ($loan->amount_paid - ($loan->amount_loaned + $loan->interest));

                $LOANS[] = [
                    'id' => $loan->id,
                    'institution_id' => $loan->institution_id,
                    'member_staff_id' => $loan->member_staff_id,
                    'month' => $loan->month,
                    'year' => $loan->year,
                    'date' => $loan->date,
                    'amount_loaned' => $loan->amount_loaned,
                    'amount_paid' => $loan->amount_paid,
                    'loan_type' => $loanType->type,
                    'rate' => $loanType->rate,
                    'interest' => $loan->interest,
                    'return_date' => $loan->return_date,
                    'added_by' => $this->_user->_getUser($loan->added_by)->name,
                    'created_at' => $loan->created_at,
                    'updated_at' => $loan->updated_at,
                    'member_name' => $loan->member_name,
                    'status' => ($total == $loan->amount_paid) ? 'Paid' : 'Owing',
                    'amount_owing' => number_format((float)$owing, 2, '.', ''),
                ];
            }

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'loans' => $LOANS
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getLoan()
    {
        try {
            // fetch loans with the id supplied
            $loan = $this->_loan->_get($this->id);

            if (!$loan) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'No record found'
                    ]
                ];
                return ['response' => $response, 'code' => 404];
            }

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'loan' => $loan
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getMemberLoans()
    {
        try {

            // fetch loans with member ID supplied
            $loans = $this->_loan->_getMemberLoans($this->institutionId, $this->staffId);

            if ($loans->isEmpty()) {
                $response = [
                    'success' => false,
                    'description' => [
                        'message' => 'No records found',
                        'loans' => []
                    ]
                ];
                return ['response' => $response, 'code' => 200];
            }

            $LOANS = [];
            foreach ($loans as $loan) {
                $total = $loan->amount_loaned + $loan->interest;
                $loanType = $this->_loanSetting->_get($loan->loan_type);

                $owing = ($loan->amount_paid - ($loan->amount_loaned + $loan->interest));

                $LOANS[] = [
                    'id' => $loan->id,
                    'institution_id' => $loan->institution_id,
                    'member_staff_id' => $loan->member_staff_id,
                    'month' => $loan->month,
                    'year' => $loan->year,
                    'date' => $loan->date,
                    'amount_loaned' => $loan->amount_loaned,
                    'amount_paid' => $loan->amount_paid,
                    'loan_type' => $loanType->type,
                    'rate' => $loanType->rate,
                    'interest' => $loan->interest,
                    'return_date' => $loan->return_date,
                    'added_by' => $this->_user->_getUser($loan->added_by)->name,
                    'created_at' => $loan->created_at,
                    'updated_at' => $loan->updated_at,
                    'status' => ($total == $loan->amount_paid) ? 'Paid' : 'Owing',
                    'amount_owing' => number_format((float)$owing, 2, '.', ''),
                ];
            }


            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'loans' => $LOANS
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getTotalAnnualLoans()
    {
        try {
            $sum = $this->_loan->_calculateYearlyTotal($this->institutionId, $this->year);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'loans' => $sum
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function deleteLoan()
    {
        try {

            // delete loan
            $this->_loan->_delete($this->id);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful'
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getLoanPayments()
    {
        try {
            $payments = $this->_loanPayment->_gets($this->id);

            $response = [
                'success' => true,
                'description' => [
                    'message' => 'successful',
                    'payments' => $payments
                ]
            ];

            return ['response' => $response, 'code' => 200];
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
}
