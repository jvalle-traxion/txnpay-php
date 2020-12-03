<?php

namespace txnpay;

use \InvalidArgumentException as Exception;
use txnpay\vars\Constants;

require_once 'vendor/autoload.php';
require_once 'constants.php';
require 'utils.php';

class TraxionPay
{
    /**
     * Core object for using TraxionPay's `cashIn` and `cashOut` functionalities.
     * See full documentation at <https://dev.traxionpay.com/developers-guide>.
     *
     * @param string    $apiKey    api_key of the merchant from the documentation.
     * @param string    $secretKey secret_key of the merchant from the documentation.
     */
    public function __construct($apiKey, $secretKey)
    {
        if (!empty($apiKey) && !empty($secretKey)) {
            $this->apiKey = $apiKey;
            $this->secretKey = $secretKey;
            $this->token = generateToken($secretKey);
            $this->authHeaders = array(
                'Authorization' => "Basic {$this->token}",
                'Content-Type' => "application/json",
            );
        } else {
            throw new Exception('`api_key` and `secret_key` is required.');
        }
    }

    /**
     * Cash In enables merchants to receive money through the application.
     * Through this feature, merchants receive payments and store it in their in-app wallet.
     *
     * POST `https://devapi.traxionpay.com/payform-link`
     * @param array $params {
     *  An array containing the parameters.
     *  
     *  @type integer  merchant_id
     *  @type string   merchant_ref_no
     *  @type string   description
     *  @type string   amount
     *  @type string   currency (optional, PHP if None)
     *  @type string   merchant_additional_data
     *  @type string   payment_method (optional)
     *  @type string   status_notification_url
     *  @type string   success_page_url
     *  @type string   failure_page_url
     *  @type string   cancel_page_url
     *  @type string   pending_page_url
     *  @type array    billing_details (optional)
     * }
     */
    public function cashIn($params)
    {
        try {
            $rawDetails = $params;
            $rawBilling = $params['billing_details'];
            unset($params['billing_details']);

            $cashInDetails = getValidatedPayload($rawDetails, Constants::KEYS['cashIn']);
            $billingDetails = getValidatedPayload($rawBilling, Constants::KEYS['billingDetails']);
            $payformData = array_merge($cashInDetails, $billingDetails);

            $data_to_hash = $payformData['merchant_ref_no'] . $payformData['amount'] . $payformData['currency'] . $payformData['description'];
            $secure_hash = hash_hmac('sha256', utf8_encode($data_to_hash), utf8_encode($this->secretKey));
            $auth_hash = hash_hmac('sha256', utf8_encode($this->apiKey), utf8_encode($this->secretKey));
            $payformData += [
                'secure_hash' => $secure_hash,
                'auth_hash' => $auth_hash,
                'alg' => 'HS256'
            ];
            $encodedPayformData = json_encode($payformData, JSON_UNESCAPED_SLASHES);
            $payload = ['form_data' => utf8_decode(base64_encode(utf8_encode($encodedPayformData)))];

            // API Call
            $response = request('POST', '/payform-link', array(), array(), $payload);
            $data = $response->url;
            return $data;
        } catch (\Throwable $th) {
            echo $th;
        }
    }

    /**
     * Retrieves a list of usable banks.
     *
     * GET `https://devapi.traxionpay.com/banks/`
     */
    public function fetchBanks()
    {
        $response = request('GET', '/banks/');
        $data = json_decode($response->body);
        return $data;
    }

    /**
     * Retrieves a list of usable bank accounts.
     *
     * GET `https://devapi.traxionpay.com/payout/bank-account/`
     */
    public function fetchBankAccounts()
    {
        $response = request('GET', '/payout/bank-account/', $this->authHeaders);
        $data = json_decode($response->body);
        return $data;
    }

    /**
     * Links or creates a new bank account.
     *
     * POST `https://devapi.traxionpay.com/payout/bank-account/`
     * @param array $params {
     *  An array containing the parameters.
     *  
     *  @type string    bank
     *  @type string    bank_type
     *  @type string    account_number
     *  @type string    account_name
     * }
     * 
     */
    public function linkBankAccount($params)
    {
        try {
            $payload = getValidatedPayload($params, Constants::KEYS['linkBankAccount']);
            $allowedBankTypes = array('savings', 'checkings');
            if (in_array($payload['bank_type'], $allowedBankTypes)) {
                $response = request('POST', '/payout/bank-account/', $this->authHeaders, $payload);
                $data = json_decode($response->body);
                return $data;
            } else {
                throw new Exception('`bank_type` must either be `savings` or `checkings`.');
            }
        } catch (\Throwable $th) {
            echo $th;
        }
    }

    /**
     * Retrieves otp for `cashOut` method.
     *
     * POST `https://devapi.traxionpay.com/bank-payout/get-otp/`
     */
    public function fetchOTP()
    {
        try {
            $data = request('POST', '/payout/bank-payout/get-otp/', $this->authHeaders);
            return json_decode($data->body);
        } catch (\Throwable $th) {
            echo $th;
        }
    }

    /**
     * The Cash Out feature allows merchants to physically retrieve the money stored in the in-app wallet.
     * To Cash Out, the merchant links a bank accout,
     * provides an OTP, and requests a payout to the bank.
     *
     * POST `https://devapi.traxionpay.com/payout/bank-payout/`
     * @param array $params {
     *  @type string    OTP
     *  @type float     amount
     *  @type integer   bank_account
     * }
     */
    public function cashOut($params)
    {
        try {
            $payload = getValidatedPayload($params, Constants::KEYS['cashOut']);
            $data = request('POST', '/payout/bank-payout/', $this->authHeaders, $payload);
            return json_decode($data->body);
        } catch (\Throwable $th) {
            echo $th;
        }
    }
}
