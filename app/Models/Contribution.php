<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Contribution extends Model
{
    private $_connection;
    protected $table = 'contributions';

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

    public function _update($id, array $payload)
    {
        $this->_connectTable()->where('id', '=', $id)->update($payload);
    }

    public function _updateMultiple(array $ids, array $payload)
    {
        $this->_connectTable()->whereIn('id', $ids)->update($payload);
    }

    public function _delete($id)
    {
        $this->_connectTable()->delete($id);
    }

    public function _gets($institutionId)
    {
        $contributions = $this->_connectTable()->where('institution_id', '=', $institutionId)
            ->orderByDesc('created_at')
            ->get();
        return $contributions;
    }

    public function _getSummary($institutionId)
    {
        $summary = $this->_connectTable()->where('institution_id', '=', $institutionId)
            ->selectRaw('month, year, count(*) as total, sum(amount) as amount')
            ->groupBy('month')
            ->groupBy('year')
            ->get();
        return $summary;
    }

    public function _get($id)
    {
        $contribution = $this->_connectTable()->where('id', '=', $id)->first();
        return $contribution;
    }

    public function _getsWithMonthAndYear($institutionId, $month, $year)
    {
        $contributions = $this->_connectTable()->select([$this->table . '.*', 'members.member_name', 'members.member_phone'])
            ->where($this->table . '.institution_id', '=', $institutionId)
            ->where($this->table . '.month', '=', $month)
            ->where($this->table . '.year', '=', $year)
            ->leftJoin('members', 'members.member_staff_id', '=', $this->table . '.member_staff_id')
            ->orderByDesc('year')
            ->get();
        return $contributions;
    }


    public function _getsWithMemberId($institutionId, $memberId)
    {
        return $this->_connectTable()->select([$this->table . '.*', 'members.member_name', 'members.member_phone'])
            ->where($this->table . '.institution_id', '=', $institutionId)
            ->where($this->table . '.member_staff_id', '=', $memberId)
            ->leftJoin('members', 'members.member_staff_id', '=', $this->table . '.member_staff_id')
            ->orderByDesc('year')
            ->get();
    }

    public function _getWithMemberIdMonthAndYear($institutionId, $memberId, $month, $year)
    {
        $contribution = $this->_connectTable()->where('institution_id', '=', $institutionId)
            ->where('member_staff_id', '=', $memberId)
            ->where('month', '=', $month)
            ->where('year', '=', $year)
            ->first();
        return $contribution;
    }

    public function _calculateYearlyTotal($institutionId, $year)
    {
        $sum = $this->_connectTable()
            ->where('institution_id', '=', $institutionId)
            ->where('year', '=', $year)
            ->sum('amount');
        return $sum;
    }

    public function _getYearlySummary($institutionId, $year)
    {
        return $this->_connectTable()
            ->where('institution_id', '=', $institutionId)
            ->where('year', '=', $year)
            ->selectRaw('month, sum(amount) as amount')
            ->groupBy('month')
            ->get();
    }
}
