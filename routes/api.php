<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1', 'middleware' => 'guest'], function () {
    Route::post('users/sign-up', 'UserController@register');
    Route::post('users/account-verification', 'UserController@accountVerification');
    Route::post('users/resend-code', 'UserController@resendCode');
    Route::post('users/reset-password', 'UserController@resetPassword');
    Route::post('users/login', 'UserController@login');
});

Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {
    // USER CONTROLLER
    Route::get('users/logout', 'UserController@logout');
    Route::post('users/profile', 'UserController@updateProfile');
    Route::post('users/change-password', 'UserController@changePassword');
    Route::post('users', 'UserController@createUser');
    Route::put('users/{id}', 'UserController@updateUser');
    Route::get('users', 'UserController@getUsers');
    Route::get('users/{id}', 'UserController@getUser');
    Route::get('users/revoke-user-access/{id}', 'UserController@revokeUserAccess');
    Route::get('users/grant-user-access/{id}', 'UserController@grantUserAccess');

    // INSTITUTION CONTROLLER
    Route::post('institution/logo', 'InstitutionController@addLogo');
    Route::post('institution', 'InstitutionController@createInstitution');
    Route::put('institution/{id}', 'InstitutionController@updateInstitution');
    Route::delete('institution/{id}', 'InstitutionController@deleteInstitution');
    Route::get('institution/{id}', 'InstitutionController@getInstitution');

    // MEMBER CONTROLLER
    Route::post('members/upload', 'MemberController@uploadMembers');
    Route::post('members', 'MemberController@createMember');
    Route::put('members/{id}', 'MemberController@updateMember');
    Route::delete('members/{id}', 'MemberController@deleteMember');
    Route::get('members', 'MemberController@getMembers');
    Route::get('members/{id}', 'MemberController@getMember');
    Route::get('members/count/total', 'MemberController@getTotalMembers');

    // UTILITY CONTROLLER
    Route::get('utility/uniform-amount', 'UtilityController@getUniformAmount');
    Route::post('utility/set-uniform-payment', 'UtilityController@setUniformPayment');
    Route::put('utility/update-uniform-payment/{id}', 'UtilityController@updateUniformPayment');
    Route::get('utility/payment-types', 'UtilityController@getPaymentTypes');

    // CONTRIBUTION CONTROLLER
    Route::get('contributions/summary', 'ContributionController@getContributionSummary');
    Route::post('contributions', 'ContributionController@addContribution');
    Route::put('contributions/{id?}', 'ContributionController@updateContribution');
    Route::get('contributions', 'ContributionController@getContributions');
    Route::get('contributions/{id}', 'ContributionController@getContribution');
    Route::get('contributions/{month}/{year}', 'ContributionController@getContributionWithMonthAndYear');
    Route::get('contributions/member/contribution/{staff_id}', 'ContributionController@getMemberContributions');
    Route::get('contributions/member/contribution/{staff_id}/{month}/{year}', 'ContributionController@getContributionWithMemberIdMonthAndYear');
    Route::delete('contributions/{id}', 'ContributionController@deleteContribution');
    Route::post('contributions/upload', 'ContributionController@uploadContributions');
    Route::get('contributions/calculate/contribution/yearly', 'ContributionController@getYearlyContributions');
    Route::get('contributions/export/members', 'ContributionController@exportContributions');
    Route::get('contributions/export/member/{staff_id}', 'ContributionController@exportMemberContributions');
    Route::get('contributions/members/no-contributions/{month}/{year}', 'ContributionController@getMembersWhoHasNotContributed');

    // LOAN CONTROLLER
    Route::get('loans', 'LoanController@getLoans');
    Route::get('loans/summary', 'LoanController@getLoanSummary');
    Route::post('loans', 'LoanController@grantLoan');
    Route::put('loans/{id}', 'LoanController@updateLoan');
    Route::get('loans/loan/{id}', 'LoanController@getLoan');
    Route::get('loans/member/loan/{staff_id}', 'LoanController@getMemberLoans');
    Route::get('loans/calculate/loan/yearly', 'LoanController@getYearlyLoans');
    Route::get('loans/payments/loan/payment/{id}', 'LoanController@getLoanPayments');
    Route::delete('loans/{id}', 'LoanController@deleteLoan');

    // LOAN SETTINGS
    Route::get('loan-settings', 'LoanSettingController@getLoanSettings');
    Route::post('loan-settings', 'LoanSettingController@setLoan');
    Route::put('loan-settings/{id}', 'LoanSettingController@updateLoanSetting');
    Route::get('loan-settings/{id}', 'LoanSettingController@getLoanSetting');
    Route::delete('loan-settings/{id}', 'LoanSettingController@deleteLoanSetting');

    // ADMIN CONTROLLER
    Route::get('admin/clients', 'AdminController@getClients');
    Route::get('admin/clients/export', 'AdminController@exportClients');
    Route::post('admin/clients/impersonate/client', 'AdminController@impersonateClient');
    Route::post('admin/clients/impersonate/admin', 'AdminController@impersonateAdmin');
    Route::get('admin/welfare/get-totals', 'AdminController@getTotals');
    Route::get('admin/welfare/get-yearly-summary', 'AdminController@getYearlySummary');

    // EXPENSE SETTINGS
    Route::get('expense-settings', 'ExpenseSettingController@getExpenseSettings');
    Route::get('expense-settings/parent', 'ExpenseSettingController@getExpenseSettingsWithParentId');
    Route::post('expense-settings', 'ExpenseSettingController@createExpenseSetting');
    Route::put('expense-settings/{id}', 'ExpenseSettingController@updateExpenseSetting');
    Route::get('expense-settings/{id}', 'ExpenseSettingController@getExpenseSetting');
    Route::delete('expense-settings/{id}', 'ExpenseSettingController@deleteExpenseSetting');


    // EXPENSES
    Route::get('expenses', 'ExpenseController@getExpenses');
    Route::post('expenses', 'ExpenseController@addExpense');
    Route::get('expenses/summary', 'ExpenseController@getExpenseSummary');
    Route::put('expenses/{id}', 'ExpenseController@updateExpense');
    Route::get('expenses/{id}', 'ExpenseController@getExpense');
    Route::delete('expenses/{id}', 'ExpenseController@deleteExpense');
    Route::get('expenses/office/office-expenses', 'ExpenseController@getOfficeExpenses');
    Route::get('expenses/members/members-expenses', 'ExpenseController@getMembersExpenses');
    Route::get('expenses/member-expenses/{staff_id}', 'ExpenseController@getMemberExpenses');
    Route::get('expenses/type/{type_id}', 'ExpenseController@getTypeExpenses');
    Route::post('expenses/upload', 'ExpenseController@uploadExpenses');
    Route::get('expenses/calculate/yearly', 'ExpenseController@getYearlyExpenses');
    Route::get('expenses/export/all', 'ExpenseController@exportAllExpenses');
    Route::get('expenses/export/office', 'ExpenseController@exportOfficeExpenses');
    Route::get('expenses/export/members', 'ExpenseController@exportMembersExpenses');
    Route::get('expenses/export/member/{staff_id}', 'ExpenseController@exportMemberExpenses');

    // INCOME SETTINGS
    Route::get('income-settings', 'IncomeSettingController@getIncomeSettings');
    Route::get('income-settings/parent', 'IncomeSettingController@getIncomeSettingsWithParentId');
    Route::post('income-settings', 'IncomeSettingController@createIncomeSetting');
    Route::put('income-settings/{id}', 'IncomeSettingController@updateIncomeSetting');
    Route::get('income-settings/{id}', 'IncomeSettingController@getIncomeSetting');
    Route::delete('income-settings/{id}', 'IncomeSettingController@deleteIncomeSetting');

    // INCOME
    Route::get('incomes', 'IncomeController@getIncomes');
    Route::post('incomes', 'IncomeController@addIncome');
    Route::get('incomes/summary', 'IncomeController@getIncomeSummary');
    Route::put('incomes/{id}', 'IncomeController@updateIncome');
    Route::get('incomes/{id}', 'IncomeController@getIncome');
    Route::delete('incomes/{id}', 'IncomeController@deleteIncome');
    Route::get('incomes/office/office-incomes', 'IncomeController@getOfficeIncomes');
    Route::get('incomes/members/members-incomes', 'IncomeController@getMembersIncomes');
    Route::get('incomes/member-incomes/{staff_id}', 'IncomeController@getMemberIncomes');
    Route::get('incomes/type/{type_id}', 'IncomeController@getTypeIncomes');
    Route::post('incomes/upload', 'IncomeController@uploadIncomes');
    Route::get('incomes/calculate/yearly', 'IncomeController@getYearlyIncome');
    Route::get('incomes/export/all', 'IncomeController@exportAllIncomes');
    Route::get('incomes/export/office', 'IncomeController@exportOfficeIncomes');
    Route::get('incomes/export/members', 'IncomeController@exportMembersIncomes');
    Route::get('incomes/export/member/{staff_id}', 'IncomeController@exportMemberIncomes');

    // DONATION SETTINGS
    Route::get('donation-settings', 'DonationSettingController@getDonationSettings');
    Route::get('donation-settings/parent', 'DonationSettingController@getDonatonSettingsWithParentId');
    Route::post('donation-settings', 'DonationSettingController@createDonationSetting');
    Route::put('donation-settings/{id}', 'DonationSettingController@updateDonationSetting');
    Route::get('donation-settings/{id}', 'DonationSettingController@getDonationSetting');
    Route::delete('donation-settings/{id}', 'DonationSettingController@deleteDonationSetting');

    // DONATION
    Route::get('donations', 'DonationController@getDonations');
    Route::post('donations', 'DonationController@addDonation');
    Route::get('donations/summary', 'DonationController@getDonationSummary');
    Route::put('donations/{id}', 'DonationController@updateDonation');
    Route::get('donations/{id}', 'DonationController@getDonation');
    Route::delete('donations/{id}', 'DonationController@deleteDonation');
    Route::get('donations/office/office-donations', 'DonationController@getOfficeDonations');
    Route::get('donations/members/members-donations', 'DonationController@getMembersDonations');
    Route::get('donations/member-donations/{staff_id}', 'DonationController@getMemberDonations');
    Route::get('donations/type/{type_id}', 'DonationController@getTypeDonations');
    Route::post('donations/upload', 'DonationController@uploadDonations');
    Route::get('donations/calculate/yearly', 'DonationController@getYearlyDonation');
    Route::get('donations/export/all', 'DonationController@exportAllDonations');
    Route::get('donations/export/office', 'DonationController@exportOfficeDonations');
    Route::get('donations/export/members', 'DonationController@exportMembersDonations');
    Route::get('donations/export/member/{staff_id}', 'DonationController@exportMemberDonations');
});
