<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Code extends Model
{
    private $_connection;
    protected $table = 'codes';

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

    public function _save(array $payload)
    {
        $this->_connectTable()->insert($payload);
    }

    public function _update($userId, array $payload)
    {
        $this->_connectTable()->where('user_id', '=', $userId)->update($payload);
    }

    public function _getCode($userId, $code)
    {
        $query = $this->_connectTable()->where('user_id', '=', $userId)
            ->where('code', '=', $code)->first();
        return $query;
    }

    public function _expireCode($userId, $code, array $payload)
    {
        $this->_connectTable()->where('user_id', '=', $userId)
            ->where('code', '=', $code)->update($payload);
    }
}
