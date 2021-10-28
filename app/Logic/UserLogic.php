<?php

namespace App\Logic;

use App\Helpers\Helper;
use App\Models\Code;
use App\Models\Institution;
use App\Models\User;
use App\Validations\UserValidation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Exception;


class UserLogic implements UserInterface
{
    private $_user;
    private $_code;
    private $_institution;
    private $_validation;

    public $userId;
    public $email;
    public $parentId;
    public $password;
    public $confirmPass;
    public $phone;
    public $name;
    public $role;
    public $isVerified;
    public $isRevoked;
    public $avatar;
    public $hasAgreed;
    public $oldPassword;

    public $code;
    public $isExpired;
    public $expiryDate;

    public function __construct(User $user, Code $code, Institution $institution)
    {
        $this->_user = $user;
        $this->_code = $code;
        $this->_institution = $institution;
    }

    public function registerUser()
    {
        $this->_validation = new UserValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateUserRegistration();
        if ($validation !== true) {
            return $validation;
        }
        // Check if email or phone already exist
        try {
            $checkEmail = $this->_user->_checkEmailExistence($this->email);
            $checkPhone = $this->_user->_checkPhoneExistence($this->phone);
            if ($checkEmail) {
                $response = [
                    'message' => 'Email ' . $this->email . ' already exists'
                ];
                return ['response' => $response, 'code' => 400];
            }
            if ($checkPhone) {
                $response = [
                    'message' => 'Phone number ' . $this->phone . ' already exists'
                ];
                return ['response' => $response, 'code' => 400];
            }
            // Prepare data for DB
            $user = [
                'email' => $this->email,
                'phone' => $this->phone,
                'name' => $this->name,
                'password' => Hash::make($this->password),
                'role' => env('ROLE_ADMIN'),
                'avatar' => Helper::generateGravatar($this->email),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            // Save user data
            $userId = $this->_user->_save($user);
            $code = Helper::generateCode();
            $codeData = [
                'user_id' => $userId,
                'code' => $code,
                'expiry' => date("Y-m-d H:i:s", strtotime('+24 hours')),
                'is_expired' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            // save activation code info
            $this->_code->_save($codeData);
            // send SMS to user with code attached
            $message = "Thank you for registering. Your verification code is " . $code . " Thank you.";
            Helper::sendSMS($this->phone, urlencode($message));
            $user = $this->_user->_getUser($userId);

            return ['response' => $user, 'code' => 201];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function loginUser(Request $request)
    {
        $this->_validation = new UserValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateUserLogin();
        if ($validation !== true) {
            return $validation;
        }

        try {
            // Attempt to log user in
            if (!Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
                $response = [
                    'message' => 'Invalid credentials.'
                ];
                return ['response' => $response, 'code' => 400];
            }
            // Attempt to log user in with activation and access
            if (!Auth::attempt(['email' => $this->email, 'password' => $this->password, 'is_verified' => $this->isVerified, 'is_revoke' => $this->isRevoked])) {
                $response = [
                    'success' => false,
                    'message' => 'Invalid credentials.'
                ];
                return ['response' => $response, 'code' => 400];
            }
            // get user
            $user = $request->user();
            $institution = $this->_institution->_getWithUserId($user->parent_id === 0 ? $user->id : $user->parent_id);
            $tokenResult = $user->createToken('Laravel Personal Access Client');
            $user['token'] = $tokenResult->accessToken;
            $user['admin_id'] = 0;

            if ($institution) {
                $user['institution_id'] = $institution->id;
            } else {
                $user['institution_id'] = 0;
            }

            return ['response' => $user, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function activateUser()
    {
        $this->_validation = new UserValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateUserActivation();
        if ($validation !== true) {
            return $validation;
        }

        try {
            // Check if user exist
            $user = $this->_user->_getUserWithEmail($this->email);
            if (!$user) {
                $response = [
                    'message' => 'Invalid user.'
                ];
                return ['response' => $response, 'code' => 400];
            }
            $userId = $user->id;

            // Check activation code if it exist
            $code = $this->_code->_getCode($userId, $this->code);
            if (!$code) {
                $response = [
                    'message' => 'Invalid verification code.'
                ];
                return ['response' => $response, 'code' => 400];
            }
            // Define a logic to update DB when code is expired
            if (strtotime(date('Y-m-d H:i:s')) > strtotime($code->expiry)) {
                $payload = [
                    'is_expired' => 1
                ];
                $this->_code->_update($userId, $payload);
                $response = [
                    'message' => 'Activation code has expired.'
                ];
                return ['response' => $response, 'code' => 400];
            }
            $payload = [
                'is_verified' => env('STATUS_ACTIVE'),
                'email_verified_at' => Carbon::now()
            ];
            // Activate User
            $this->_user->_update($userId, $payload);
            //We have to force expire the code after user is done activating
            $this->_code->_expireCode($userId, $this->code, ['is_expired' => 1]);
            return ['response' => null, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function resendActivationCode()
    {
        $this->_validation = new UserValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateUserCodeResend();
        if ($validation !== true) {
            return $validation;
        }

        try {
            $user = $this->_user->_getUserWithEmail($this->email);
            if (!$user) {
                $response = [
                    'message' => 'Invalid user.'
                ];
                return ['response' => $response, 'code' => 400];
            }
            $userId = $user->id;
            $phone = $user->phone;

            // check if user is activated already
            if ($user->is_verified == env('STATUS_ACTIVE')) {
                $response = [
                    'message' => 'Your account is already activated.'
                ];
                return ['response' => $response, 'code' => 400];
            }
            // generate code if user is not activated
            $code = Helper::generateCode();
            $codeData = [
                'code' => $code,
                'expiry' => date("Y-m-d H:i:s", strtotime('+24 hours')),
                'is_expired' => 0
            ];
            // save code
            $this->_code->_update($userId, $codeData);
            // send sms to user with code
            $message = "Your verification code is " . $code . " Thank you.";
            Helper::sendSMS($phone, urlencode($message));
            return ['response' => ['email' => $this->email], 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function resetPassword()
    {
        $this->_validation = new UserValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateUserCodeResend();
        if ($validation !== true) {
            return $validation;
        }

        try {
            $user = $this->_user->_getUserWithEmail($this->email);
            if (!$user) {
                $response = [
                    'message' => 'Invalid user.'
                ];
                return ['response' => $response, 'code' => 400];
            }
            $userId = $user->id;
            $phone = $user->phone;

            // Generate new password
            $password = Helper::generateRandomPassword();
            $payload = ['password' => Hash::make($password)];

            $this->_user->_update($userId, $payload);
            $message = "Your new password is " . $password . " Thank you.";
            Helper::sendSMS($phone, urlencode($message));
            return ['response' => null, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function changePassword(Request $request)
    {
        $this->_validation = new UserValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateUserPassword();
        if ($validation !== true) {
            return $validation;
        }

        try {
            $user = $request->user();

            // check if old password really exist
            if (!Hash::check($this->oldPassword, $user->password)) {
                $response = [
                    'message' => 'Old password is incorrect'
                ];
                return ['response' => $response, 'code' => 400];
            }

            // save new password
            $this->_user->_update($user->id, ['password' => Hash::make($this->password)]);

            return ['response' => 'none', 'code' => 201];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function logoutUser(Request $request)
    {
        try {
            $request->user()->token()->revoke();
            $request->user()->token()->delete();
            return ['response' => 'none', 'code' => 204];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateUserProfile(Request $request)
    {
        $this->_validation = new UserValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateUserProfile();
        if ($validation !== true) {
            return $validation;
        }

        try {

            $avatar = Helper::generateGravatar($this->email);
            // save user input
            $user = [
                'email' => $this->email,
                'phone' => $this->phone,
                'name' => $this->name,
                'avatar' => $avatar,
                'updated_at' => Carbon::now()
            ];
            $userId = $request->user()->id;
            $this->_user->_update($userId, $user);

            $user = $this->_user->_getUser($userId);
            return ['response' => $user, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function revokeOrGrantUserAccess($type)
    {
        try {
            // revoke user access
            $this->_user->_update($this->userId, ['is_revoke' => $this->isRevoked]);

            $users = $this->_user->_getUsersWithParentId($this->parentId);

            return ['response' => $users, 'code' => 201];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createUser(Request $request)
    {
        $this->_validation = new UserValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateUserProfile();
        if ($validation !== true) {
            return $validation;
        }

        try {
            $checkEmail = $this->_user->_checkEmailExistence($this->email);
            $checkPhone = $this->_user->_checkPhoneExistence($this->phone);
            if ($checkEmail) {
                $response = [
                    'message' => 'Email ' . $this->email . ' already exists'
                ];
                return ['response' => $response, 'code' => 400];
            }
            if ($checkPhone) {
                $response = [
                    'message' => 'Phone number ' . $this->phone . ' already exists'
                ];
                return ['response' => $response, 'code' => 400];
            }

            $user = $request->user();
            // generate random password
            $password = Helper::generateRandomPassword();

            // save user
            $payload = [
                'parent_id' => $user->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'is_verified' => $this->isVerified,
                'role' => $this->role,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'password' => Hash::make($password),
                'avatar' => Helper::generateGravatar($this->email),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            $this->_user->_save($payload);

            $institution = $this->_institution->_getWithUserId($user->id);

            $message = "Your email is " . $this->email . " and password is " . $password . ". Thank you.";
            Helper::sendSMS($this->phone, urlencode($message), $institution->sender_id);

            return ['response' => 'none', 'code' => 201];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateUser(Request $request)
    {
        $this->_validation = new UserValidation($this);
        // Validate user inputs
        $validation = $this->_validation->__validateUserProfile();
        if ($validation !== true) {
            return $validation;
        }

        try {
            $user = $request->user();
            // update user
            $payload = [
                'parent_id' => $user->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'avatar' => Helper::generateGravatar($this->email),
                'updated_at' => Carbon::now()
            ];
            $this->_user->_update($this->userId, $payload);

            $users = $this->_user->_getUsersWithParentId($user->id);

            return ['response' => $users, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getUsers(Request $request)
    {
        try {
            // obtain the parent id which is also the admin id
            $parentId = $request->user()->id;

            $users = $this->_user->_getUsersWithParentId($parentId);

            return ['response' => $users, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getUser()
    {
        try {
            $user = $this->_user->_getUser($this->userId);

            if (!$user) {
                return ['response' => null, 'code' => 404];
            }

            return ['response' => $user, 'code' => 200];
        } catch (Exception $e) {
            throw $e;
        }
    }
}
