<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    private $_connection;
    protected $table = 'users';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->_connection = DB::connection('mysql');
    }

    private function _connectTable()
    {
        $table = $this->_connection->table($this->table);
        return $table;
    }

    public function _checkEmailExistence($email)
    {
        $query = $this->_connectTable()->where('email', '=', $email)->first();
        return ($query) ? true : false;
    }

    public function _checkPhoneExistence($phone)
    {
        $query = $this->_connectTable()->where('phone', '=', $phone)->first();
        return ($query) ? true : false;
    }

    public function _checkActivation($userId, $isVerified = 1)
    {
        $query = $this->_connectTable()->where('id', '=', $userId)
            ->where('is_verified', '=', $isVerified)->first();
        return ($query) ? true : false;
    }

    public function _save(array $payload)
    {
        $userId = $this->_connectTable()->insertGetId($payload);
        return $userId;
    }

    public function _update($userId, array $payload)
    {
        $this->_connectTable()->where('id', '=', $userId)->update($payload);
    }

    public function _updateMultiple(array $ids, array $payload)
    {
        $this->_connectTable()->whereIn('id', $ids)->update($payload);
    }

    public function _getUser($userId)
    {
        $query = $this->_connectTable()->where('id', '=', $userId)->first();
        return $query;
    }

    public function _getUserWithEmail($email)
    {
        $query = $this->_connectTable()->where('email', '=', $email)->first();
        return $query;
    }

    public function _getUsersWithParentId($parentId)
    {
        $query = $this->_connectTable()->where('parent_id', '=', $parentId)->get();
        return $query;
    }
}
