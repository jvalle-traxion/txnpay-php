# TraxionPay PHP SDK

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)

## Installation
```sh
composer install txnpay
```

## Usage

#### Initialize
After installing, initialize by importing the package and using the [public and secret keys](https://dev.traxionpay.com/developers-guide).
```php
use txnpay\TraxionPay;

$traxionpay = new TraxionPay($yourApiKey, $yourSecretKey);
```
#### Cash in
```php
# Sample arguments are the bare minimum for cash_in
$response = $traxionpay->cashIn([
    'merchant_id' => 6328,
    'merchant_ref_no' => 'ABC123DEF456',
    'merchant_additional_data' => (object)array("payment_code" => "ABC123DEF456"),
    'description' => 'My test payment',
    'amount' => 1500.0,
    'status_notification_url' => 'https://devapi.traxionpay.com/callback',
    'success_page_url' => 'https://dev.traxionpay.com', 
    'failure_page_url' => 'https://dev.traxionpay.com', 
    'cancel_page_url' => 'https://dev.traxionpay.com',
    'pending_page_url' => 'https://dev.traxionpay.com',
    'billing_details' => array(
      'billing_email' => 'johndoe@gmail.com'
    )
]);
```
#### Cash out
```php
$otp = $traxionpay->fetchOTP();
$response = $traxionpay->cashOut([
    'OTP' => $otp->code,
    'amount' => 100.0,
    'bank_account' => 413
]);
```
#### Link a bank account
```php
$response = $traxionpay->linkBankAccount([
  'bank' => '6311',
  'bank_type' => 'savings',
  'account_number' => '9012345678',
  'account_name' => 'John Doe'
]);
```
#### Fetch Cash Out OTP
```php
$otp = $traxionpay->fetchOTP();
```
#### Fetch bank accounts
```php
$bankAccounts = $traxionpay->fetchBankAccounts();
```
#### Fetch banks
```php
$banks = $traxionpay->fetchBanks();
```
