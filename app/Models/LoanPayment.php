<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LoanPayment extends Model
{
    private $_connection;
    protected $table = 'loan_payments';

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

    public function _gets($loanId)
    {
        $query = $this->_connectTable()->where('loan_id', '=', $loanId)->get();
        return $query;
    }
}
