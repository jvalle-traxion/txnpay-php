<?php

namespace txnpayTest;

require_once 'src/traxionpay.php';

use PHPUnit\Framework\TestCase;
use txnpay\TraxionPay;

class TraxionPayTest extends TestCase
{
    private $secretKey = 'cxl+hwc%97h6+4#lx1au*ut=ml+=!fx85w94iuf*06=rf383xs';
    private $apiKey = '7)5dmcfy^dp*9bdrcfcm\$k-n=p7b!x(t)_f^i8mxl@v_+rno*x';
    private $api;

    public function setUp(): void
    {
        $this->api = new TraxionPay($this->apiKey, $this->secretKey);
    }

    public function testInit()
    {
        // test if object is instantiated
        $this->assertIsObject($this->api);
    }

    public function testCashIn()
    {
        $apiUrl = 'https://devapi.traxionpay.com/callback';
        $siteUrl = 'https://dev.traxionpay.com';
        $billing_details = [
            "billing_email" => "dwanevalle.dv@gmail.com"
        ];
          
        $data = $this->api->cashIn([
            'merchant_id' => 6328,
            'merchant_ref_no' => 'ABC123DEF456',
            'merchant_additional_data' => (object)array("payment_code" => "ABC123DEF456"),
            'description' => 'My test payment',
            'amount' => 1500.0,
            'status_notification_url' => $apiUrl,
            'success_page_url' => $siteUrl, 
            'failure_page_url' => $siteUrl, 
            'cancel_page_url' => $siteUrl,
            'pending_page_url' => $siteUrl,
            'billing_details' => $billing_details
        ]);

        $this->assertStringContainsString('https://dev.traxionpay.com/payme/?data=', $data);
    }

    public function testFetchBanks()
    {
        $data = $this->api->fetchBanks();
        // test if banks are retrieved
        $this->assertIsArray($data);
        $this->assertGreaterThan(0, $data);
    }

    public function testFetchOTP()
    {
        $data = $this->api->fetchOTP();
        // test if otp is retrieved
        $this->assertObjectHasAttribute('code', $data);
    }

    public function testLinkBankAccount()
    {
        $data = $this->api->linkBankAccount([
            'bank' => '6311',
            'bank_type' => 'savings',
            'account_number' => '123456789',
            'account_name' => 'John Doe'
        ]);
        
        // test if request successful
        $this->assertObjectHasAttribute('id', $data);
        $this->assertObjectHasAttribute('bank_name', $data);
        $this->assertObjectHasAttribute('account_number', $data);
    }

    public function testCashOut()
    {
        $otp = $this->api->fetchOTP();
        $data = $this->api->cashOut([
            'OTP' => $otp->code,
            'amount' => 100.0,
            'bank_account' => 433
        ]);
        // test if request successful
        $this->assertObjectHasAttribute('ref_no', $data);
        $this->assertObjectHasAttribute('transaction_id', $data);
        $this->assertObjectHasAttribute('remittance_id', $data);
    }
}
