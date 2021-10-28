<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Logic\UserLogic;

class UserController extends Controller
{
    private $_userLogic;

    public function __construct(UserLogic $userLogic)
    {
        $this->_userLogic = $userLogic;
    }

    // @route  POST api/v1/users/sign-up
    // @desc   Register user
    // @access Public
    public function register(Request $request)
    {
        // Declare variables
        $this->_userLogic->name = trim($request->input('name'));
        $this->_userLogic->email = trim($request->input('email'));
        $this->_userLogic->phone = trim($request->input('phone'));
        $this->_userLogic->password = trim($request->input('password'));
        $this->_userLogic->confirmPass = trim($request->input('confirm_password'));
        $this->_userLogic->hasAgreed = trim($request->input('agree'));

        // Register user
        $response = $this->_userLogic->registerUser();
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/users/account-verification
    // @desc   Verify user account
    // @access Public
    public function accountVerification(Request $request)
    {
        // Declare variables
        $this->_userLogic->email = trim($request->input('email'));
        $this->_userLogic->code = trim($request->input('code'));

        // Verify user account
        $response = $this->_userLogic->activateUser();
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/users/resend-code
    // @desc   Resent verification code
    // @access Public
    public function resendCode(Request $request)
    {
        $this->_userLogic->email = trim($request->input('email'));
        $response = $this->_userLogic->resendActivationCode();
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/users/reset-password
    // @desc   Reset user password
    // @access Public
    public function resetPassword(Request $request)
    {
        $this->_userLogic->email = trim($request->input('email'));
        $response = $this->_userLogic->resetPassword();
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/users/login
    // @desc   Log user in
    // @access Public
    public function login(Request $request)
    {
        $this->_userLogic->email = trim($request->input('email'));
        $this->_userLogic->password = trim($request->input('password'));
        $this->_userLogic->isVerified = env('STATUS_ACTIVE');
        $this->_userLogic->isRevoked = env('IS_ACTIVE');
        $response = $this->_userLogic->loginUser($request);
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/users/logout
    // @desc   Log user out
    // @access Private
    public function logout(Request $request)
    {
        $response = $this->_userLogic->logoutUser($request);
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/users/profile
    // @desc   Update profile
    // @access Private
    public function updateProfile(Request $request)
    {
        $this->_userLogic->name = trim($request->input('name'));
        $this->_userLogic->email = trim($request->input('email'));
        $this->_userLogic->phone = trim($request->input('phone'));
        $response = $this->_userLogic->updateUserProfile($request);
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/users/change-password
    // @desc   Change Password
    // @access Private
    public function changePassword(Request $request)
    {
        $this->_userLogic->oldPassword = trim($request->input('old_password'));
        $this->_userLogic->password = trim($request->input('new_password'));
        $this->_userLogic->confirmPass = trim($request->input('confirm_password'));
        $response = $this->_userLogic->changePassword($request);
        return response()->json($response['response'], $response['code']);
    }

    // @route  POST api/v1/users
    // @desc   Post User
    // @access Private
    public function createUser(Request $request)
    {
        $this->_userLogic->name = trim($request->input('name'));
        $this->_userLogic->email = trim($request->input('email'));
        $this->_userLogic->phone = trim($request->input('phone'));
        $this->_userLogic->isVerified = env('STATUS_ACTIVE');
        $this->_userLogic->role = env('ROLE_USER');
        $response = $this->_userLogic->createUser($request);
        return response()->json($response['response'], $response['code']);
    }

    // @route  PUT api/v1/users/:user_id
    // @desc   Update User
    // @access Private
    public function updateUser($userId, Request $request)
    {
        $this->_userLogic->name = trim($request->input('name'));
        $this->_userLogic->email = trim($request->input('email'));
        $this->_userLogic->phone = trim($request->input('phone'));
        $this->_userLogic->userId = $userId;
        $response = $this->_userLogic->updateUser($request);
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/users
    // @desc   Get User
    // @access Private
    public function getUsers(Request $request)
    {
        $response = $this->_userLogic->getUsers($request);
        return response()->json($response['response'], $response['code']);
    }
    // @route  GET api/v1/users/:id
    // @desc   Get User
    // @access Private
    public function getUser($id)
    {
        $this->_userLogic->userId = $id;
        $response = $this->_userLogic->getUser();
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/users/revoke-user-access/:user_id
    // @desc   Revoke User Access
    // @access Private
    public function revokeUserAccess($id, Request $request)
    {
        $this->_userLogic->userId = $id;
        $this->_userLogic->isRevoked = env('IS_REVOKE');
        $this->_userLogic->parentId = $request->user()->id;
        $response = $this->_userLogic->revokeOrGrantUserAccess('revoked');
        return response()->json($response['response'], $response['code']);
    }

    // @route  GET api/v1/users/grant-user-access/:user_id
    // @desc   Grant User Access
    // @access Private
    public function grantUserAccess($id, Request $request)
    {
        $this->_userLogic->userId = $id;
        $this->_userLogic->isRevoked = env('IS_ACTIVE');
        $this->_userLogic->parentId = $request->user()->id;
        $response = $this->_userLogic->revokeOrGrantUserAccess('granted');
        return response()->json($response['response'], $response['code']);
    }
}
