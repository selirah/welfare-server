<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Member extends Model
{
    private $_connection;
    protected $table = 'members';

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

    public function _update($id, $payload)
    {
        $this->_connectTable()->where('id', '=', $id)->update($payload);
    }

    public function _delete($id)
    {
        $this->_connectTable()->delete($id);
    }

    public function _gets($institutionId)
    {
        $members = $this->_connectTable()->select([$this->table . '.*', 'fixed_payment.amount'])
            ->where($this->table . '.institution_id', '=', $institutionId)
            ->leftJoin('fixed_payment', 'fixed_payment.member_staff_id', '=', $this->table . '.member_staff_id')
            ->orderByDesc('created_at')->get();
        return $members;
    }

    public function _get($id)
    {
        $member = $this->_connectTable()->select([$this->table . '.*', 'fixed_payment.amount'])
            ->where($this->table . '.id', '=', $id)
            ->leftJoin('fixed_payment', 'fixed_payment.member_staff_id', '=', $this->table . '.member_staff_id')
            ->first();
        return $member;
    }

    public function _getWithStaffId($institutionId, $staffId)
    {
        $member = $this->_connectTable()->where('institution_id', '=', $institutionId)
            ->where('member_staff_id', '=', $staffId)->first();
        return $member;
    }

    public function _getTotal($institutionId)
    {
        $total = $this->_connectTable()->where('institution_id', '=', $institutionId)->count();
        return $total;
    }
}
