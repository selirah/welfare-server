<?php
/**
 * Created by PhpStorm.
 * User: selirah
 * Date: 6/18/2019
 * Time: 10:23 AM
 */

namespace App\Interfaces;

use Illuminate\Http\Request;

interface UserInterface
{
    public function registerUser();

    public function loginUser(Request $request);

    public function activateUser();

    public function resendActivationCode();

    public function resetPassword();

    public function changePassword(Request $request);

    public function logoutUser(Request $request);

    public function updateUserProfile(Request $request);

    public function revokeOrGrantUserAccess($type);

    public function createUser(Request $request);

    public function updateUser(Request $request);

    public function getUsers(Request $request);

    public function getUser();
}
