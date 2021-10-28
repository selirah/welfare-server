<?php

namespace App\Interfaces;

interface IncomeSettingInterface
{
    public function addIncomeSetting();

    public function updateIncomeSetting();

    public function deleteIncomeSetting();

    public function getIncomeSettings();

    public function getIncomeSetting();

    public function getIncomeSettingsWithParentId();
}
