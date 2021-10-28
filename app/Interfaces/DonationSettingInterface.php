<?php

namespace App\Interfaces;


interface DonationSettingInterface
{
    public function addDonationSetting();

    public function updateDonationSetting();

    public function deleteDonationSetting();

    public function getDonationSettings();

    public function getDonationSetting();

    public function getDonationSettingsWithParentId();
}
