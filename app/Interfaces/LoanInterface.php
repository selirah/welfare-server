<?php
/**
 * Created by PhpStorm.
 * User: selirah
 * Date: 6/24/2019
 * Time: 9:28 AM
 */

namespace App\Interfaces;


interface LoanInterface
{
    public function grantLoan();

    public function updateLoan();

    public function getLoanSummary();

    public function getLoans();

    public function getLoan();

    public function getMemberLoans();

    public function getTotalAnnualLoans();

    public function deleteLoan();

    public function getLoanPayments();

}
