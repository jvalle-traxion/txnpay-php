<?php

namespace txnpay\vars;

class Constants {
  const BASE_URL = 'https://devapi.traxionpay.com';
  const KEYS = array(
    'cashIn' => [
      'merchant_id' => [
        'required' => true,
        'type' => 'integer'
      ],
      'merchant_ref_no' => [
        'required' => true,
        'type' => 'string'
      ],
      'description' => [
        'required' => true,
        'type' => 'string'
      ],
      'amount' => [
        'required' => true,
        'type' => 'double'
      ],
      'currency' => [
        'required' => false,
        'type' => 'string',
        'default' => 'PHP'
      ],
      'merchant_additional_data' => [
        'required' => true,
        'type' => 'string'
      ],
      'payment_method' => [
        'required' => false,
        'type' => 'string'
      ],
      'status_notification_url' => [
        'required' => true,
        'type' => 'string'
      ],
      'success_page_url' => [
        'required' => true,
        'type' => 'string'
      ],
      'failure_page_url' => [
        'required' => true,
        'type' => 'string'
      ],
      'cancel_page_url' => [
        'required' => true,
        'type' => 'string'
      ],
      'pending_page_url' => [
        'required' => true,
        'type' => 'string'
      ],
    ],
    'billingDetails' => [
      'billing_email' => [
        'required' => false,
        'type' => 'string'
      ],
      'billing_first_name' => [
        'required' => false,
        'type' => 'string'
      ],
      'billing_last_name' => [
        'required' => false,
        'type' => 'string'
      ],
      'billing_middle_name' => [
        'required' => false,
        'type' => 'string'
      ],
      'billing_phone' => [
        'required' => false,
        'type' => 'string'
      ],
      'billing_mobile' => [
        'required' => false,
        'type' => 'string'
      ],
      'billing_address' => [
        'required' => false,
        'type' => 'string'
      ],
      'billing_address2' => [
        'required' => false,
        'type' => 'string'
      ],
      'billing_city' => [
        'required' => false,
        'type' => 'string'
      ],
      'billing_state' => [
        'required' => false,
        'type' => 'string'
      ],
      'billing_zip"' => [
        'required' => false,
        'type' => 'string'
      ],
      'billing_country' => [
        'required' => false,
        'type' => 'string',
        'default' => 'PH'
      ],
      'billing_remark' => [
        'required' => false,
        'type' => 'string'
      ],
    ],
    'linkBankAccount' => [
      'bank' => [
        'required' => true,
        'type' => 'string'
      ],
      'bank_type' => [
        'required' => true,
        'type' => 'string'
      ],
      'account_number' => [
        'required' => true,
        'type' => 'string'
      ],
      'account_name' => [
        'required' => true,
        'type' => 'string'
      ],
    ],
    'cashOut' => [
      'OTP' => [
        'required' => true,
        'type' => 'string'
      ],
      'amount' => [
        'required' => true,
        'type' => 'double'
      ],
      'bank_account' => [
        'required' => true,
        'type' => 'integer'
      ],
    ]
  );
}