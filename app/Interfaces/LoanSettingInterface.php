<?php
/**
 * Created by PhpStorm.
 * User: selirah
 * Date: 7/6/2019
 * Time: 8:35 PM
 */

namespace App\Interfaces;


interface LoanSettingInterface
{
    public function setLoan();

    public function updateLoanSetting();

    public function getLoanSettings();

    public function getLoanSetting();

    public function deleteLoanSetting();

}
