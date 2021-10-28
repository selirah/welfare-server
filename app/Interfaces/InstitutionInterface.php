<?php
/**
 * Created by PhpStorm.
 * User: selirah
 * Date: 6/19/2019
 * Time: 1:29 PM
 */

namespace App\Interfaces;


interface InstitutionInterface
{
    public function getInstitution();

    public function createInstitution();

    public function updateInstitution();

    public function deleteInstitution();

    public function addLogo();
}
