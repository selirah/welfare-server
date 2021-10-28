<?php

/**
 * Created by PhpStorm.
 * User: selirah
 * Date: 6/22/2019
 * Time: 9:18 AM
 */

namespace App\Interfaces;


interface ContributionInterface
{
    public function addContribution();

    public function editContribution();

    public function getContributionSummary();

    public function getContributions();

    public function getContribution();

    public function getContributionsWithMonthAndYear();

    public function getMemberContributions();

    public function getContributionWithMemberIdMonthAndYear();

    public function deleteContribution();

    public function uploadContributions();

    public function calculateYearlyTotalContributions();

    public function exportContributions();

    public function getMembersWithNoContributions();

    public function exportMemberContributions();
}
