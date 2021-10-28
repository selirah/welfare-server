<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Loan extends Model
{
    private $_connection;
    protected $table = 'loans';

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
        $this->_connectTable()->where('id', $id)->update($payload);
    }

    public function _delete($id)
    {
        $this->_connectTable()->delete($id);
    }

    public function _gets($institutionId, $month, $year)
    {
        if (!empty($month) && !empty($year)) {
            return $this->_connectTable()
                ->select([$this->table . '.*', 'members.member_staff_id', 'members.member_name'])
                ->where($this->table . '.institution_id', '=', $institutionId)
                ->where($this->table . '.month', '=', $month)
                ->where($this->table . '.year', '=', $year)
                ->leftJoin('members', 'members.member_staff_id', '=', $this->table . '.member_staff_id')
                ->orderByDesc('created_at')
                ->get();
        }
        return $this->_connectTable()
            ->select([$this->table . '.*', 'members.member_staff_id', 'members.member_name'])
            ->where($this->table . '.institution_id', '=', $institutionId)
            ->leftJoin('members', 'members.member_staff_id', '=', $this->table . '.member_staff_id')
            ->orderByDesc('created_at')
            ->get();
    }

    public function _get($id)
    {
        $loan = $this->_connectTable()->select([$this->table . '.*', 'members.member_staff_id', 'members.member_name'])
            ->where($this->table . '.id', '=', $id)
            ->leftJoin('members', 'members.member_staff_id', '=', $this->table . '.member_staff_id')
            ->first();
        return $loan;
    }

    public function _getMemberLoans($institutionId, $memberId)
    {
        $loans = $this->_connectTable()->where('institution_id', '=', $institutionId)
            ->where('member_staff_id', '=', $memberId)
            ->orderByDesc('year')
            ->get();
        return $loans;
    }

    public function _calculateYearlyTotal($institutionId, $year)
    {
        $sum = $this->_connectTable()
            ->where('institution_id', '=', $institutionId)
            ->where('year', '=', $year)
            ->sum('amount_loaned');
        return $sum;
    }

    public function _getSummary($institutionId)
    {
        $summary = $this->_connectTable()->where('institution_id', '=', $institutionId)
            ->selectRaw('month, year, count(*) as total, sum(amount_loaned) as amount_loaned, sum(amount_paid) as amount_paid')
            ->groupBy('month')
            ->groupBy('year')
            ->get();
        return $summary;
    }

    public function _getYearlySummary($institutionId, $year)
    {
        return $this->_connectTable()
            ->where('institution_id', '=', $institutionId)
            ->where('year', '=', $year)
            ->selectRaw('month, sum(amount_loaned) as amount')
            ->groupBy('month')
            ->get();
    }
}
