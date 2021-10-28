<?php
/**
 * Created by PhpStorm.
 * User: selirah
 * Date: 6/21/2019
 * Time: 9:52 AM
 */

namespace App\Interfaces;


interface UtilityInterface
{
    public function setUniformPayment();

    public function updateUniformPayment();

    public function getPaymentTypes();

    public function getUniformAmount();

}
