<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FixedPayment extends Model
{
    private $_connection;
    protected $table = 'fixed_payment';

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

    public function _update($institutionId, $memberStaffId, array $payload)
    {
        $this->_connectTable()->where('institution_id', '=', $institutionId)
            ->where('member_staff_id', '=', $memberStaffId)
            ->update($payload);
    }

    public function _get($institutionId, $memberStaffId)
    {
        $query = $this->_connectTable()->where('institution_id', '=', $institutionId)
            ->where('member_staff_id', '=', $memberStaffId)
            ->first();
        return $query;
    }

    public function _delete($id)
    {
        $this->_connectTable()->delete($id);
    }
}
