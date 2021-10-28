<?php
/**
 * Created by PhpStorm.
 * User: selirah
 * Date: 6/21/2019
 * Time: 7:37 AM
 */

namespace App\Interfaces;


interface MemberInterface
{
    public function getMembers();

    public function getMember();

    public function createMember();

    public function updateMember();

    public function deleteMember();

    public function uploadMembers();

    public function getTotalMembers();
}
