<?php

namespace App\Interfaces;


use Illuminate\Http\Request;

interface AdminInterface
{
    public function getClients();

    public function impersonateClient(Request $request);

    public function impersonateAdmin(Request $request);

    public function exportClients();

    public function getTotals();

    public function getYearlySummary();
}
