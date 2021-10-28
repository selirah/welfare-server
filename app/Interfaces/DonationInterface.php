<?php

namespace App\Interfaces;


interface DonationInterface
{
    public function addDonation();

    public function updateDonation();

    public function getDonations();

    public function getDonation();

    public function getDonationsSummary();

    public function getOfficeDonations();

    public function getMembersDonations();

    public function getMemberDonations();

    public function getTypeDonations();

    public function uploadDonations();

    public function deleteDonation();

    public function calculateYearlyTotalDonation();

    public function exportAllDonations();

    public function exportMembersDonations();

    public function exportOfficeDonations();

    public function exportMemberDonations();
}
