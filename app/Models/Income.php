<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Income extends Model
{
    private $_connection;
    protected $table = 'incomes';

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

    public function _gets($institutionId, $month, $year)
    {
        if (!empty($month) && !empty($year)) {
            return $this->_connectTable()
                ->select([$this->table . '.*', 'income_settings.type'])
                ->where($this->table . '.institution_id', '=', $institutionId)
                ->where('month', '=', $month)
                ->where('year', '=', $year)
                ->leftJoin('income_settings', 'income_settings.id', '=', $this->table . '.type_id')
                ->orderByDesc('created_at')
                ->get();
        }
        return $this->_connectTable()
            ->select([$this->table . '.*', 'income_settings.type'])
            ->where($this->table . '.institution_id', '=', $institutionId)
            ->leftJoin('income_settings', 'income_settings.id', '=', $this->table . '.type_id')
            ->orderByDesc('created_at')
            ->get();
    }

    public function _get($id)
    {
        return $this->_connectTable()->where('id', '=', $id)->first();
    }

    public function _getSummary($institutionId)
    {
        return $this->_connectTable()
            ->where('institution_id', '=', $institutionId)
            ->selectRaw('month, year, count(*) as total, sum(amount) as amount')
            ->groupBy('month')
            ->groupBy('year')
            ->get();
    }

    public function _getOfficeIncomes($institutionId, $month, $year)
    {
        if (!empty($month) && !empty($year)) {
            return $this->_connectTable()
                ->select([$this->table . '.*', 'income_settings.type'])
                ->where($this->table . '.institution_id', '=', $institutionId)
                ->where('month', '=', $month)
                ->where('year', '=', $year)
                ->where('member_staff_id', '=', 'office')
                ->leftJoin('income_settings', 'income_settings.id', '=', $this->table . '.type_id')
                ->orderByDesc('created_at')
                ->get();
        }
        return $this->_connectTable()
            ->select([$this->table . '.*', 'income_settings.type'])
            ->where($this->table . '.institution_id', '=', $institutionId)
            ->where('member_staff_id', '=', 'office')
            ->leftJoin('income_settings', 'income_settings.id', '=', $this->table . '.type_id')
            ->orderByDesc('created_at')
            ->get();
    }

    public function _getMembersIncomes($institutionId, $month, $year)
    {
        if (!empty($month) && !empty($year)) {
            return $this->_connectTable()
                ->select([$this->table . '.*', 'income_settings.type', 'members.member_name', 'members.member_phone'])
                ->where($this->table . '.institution_id', '=', $institutionId)
                ->where('month', '=', $month)
                ->where('year', '=', $year)
                ->where($this->table . '.member_staff_id', '!=', 'office')
                ->leftJoin('income_settings', 'income_settings.id', '=', $this->table . '.type_id')
                ->leftJoin('members', 'members.member_staff_id', '=', $this->table . '.member_staff_id')
                ->orderByDesc('created_at')
                ->get();
        }
        return $this->_connectTable()
            ->select([$this->table . '.*', 'income_settings.type', 'members.member_name', 'members.member_phone'])
            ->where($this->table . '.institution_id', '=', $institutionId)
            ->where($this->table . '.member_staff_id', '!=', 'office')
            ->leftJoin('income_settings', 'income_settings.id', '=', $this->table . '.type_id')
            ->leftJoin('members', 'members.member_staff_id', '=', $this->table . '.member_staff_id')
            ->orderByDesc('created_at')
            ->get();
    }

    public function _getMemberIncomes($institutionId, $staffId, $month, $year)
    {
        if (!empty($month) && !empty($year)) {
            return $this->_connectTable()
                ->select([$this->table . '.*', 'income_settings.type', 'members.member_name', 'members.member_phone'])
                ->where($this->table . '.institution_id', '=', $institutionId)
                ->where('month', '=', $month)
                ->where('year', '=', $year)
                ->where($this->table . '.member_staff_id', '=', $staffId)
                ->leftJoin('income_settings', 'income_settings.id', '=', $this->table . '.type_id')
                ->leftJoin('members', 'members.member_staff_id', '=', $this->table . '.member_staff_id')
                ->orderByDesc('created_at')
                ->get();
        }
        return $this->_connectTable()
            ->select([$this->table . '.*', 'income_settings.type', 'members.member_name', 'members.member_phone'])
            ->where($this->table . '.institution_id', '=', $institutionId)
            ->where($this->table . '.member_staff_id', '=', $staffId)
            ->leftJoin('income_settings', 'income_settings.id', '=', $this->table . '.type_id')
            ->leftJoin('members', 'members.member_staff_id', '=', $this->table . '.member_staff_id')
            ->orderByDesc('created_at')
            ->get();
    }

    public function _getsWithType($institutionId, $typeId, $month, $year)
    {
        if (!empty($month) && !empty($year)) {
            return $this->_connectTable()
                ->select([$this->table . '.*', 'income_settings.type'])
                ->where($this->table . '.institution_id', '=', $institutionId)
                ->where('type_id', '=', $typeId)
                ->where('month', '=', $month)
                ->where('year', '=', $year)
                ->leftJoin('income_settings', 'income_settings.id', '=', $this->table . '.type_id')
                ->orderByDesc('created_at')
                ->get();
        }
        return $this->_connectTable()
            ->select([$this->table . '.*', 'income_settings.type'])
            ->where($this->table . '.institution_id', '=', $institutionId)
            ->where('type_id', '=', $typeId)
            ->leftJoin('income_settings', 'income_settings.id', '=', $this->table . '.type_id')
            ->orderByDesc('created_at')
            ->get();
    }

    public function _calculateYearlyTotal($institutionId, $year)
    {
        return $this->_connectTable()
            ->where('institution_id', '=', $institutionId)
            ->where('year', '=', $year)
            ->sum('amount');
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
