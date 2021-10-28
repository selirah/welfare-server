<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LoanSetting extends Model
{
    private $_connection;
    protected $table = 'loan_settings';

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

    public function _gets($institutionId)
    {
        $query = $this->_connectTable()->where('institution_id', '=', $institutionId)->get();
        return $query;
    }

    public function _get($id)
    {
        $query = $this->_connectTable()->where('id', '=', $id)->first();
        return $query;
    }
}
