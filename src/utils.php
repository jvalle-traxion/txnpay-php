<?php

require_once 'vendor/autoload.php';

use \InvalidArgumentException as Exception;
use GuzzleHttp\Client;
use txnpay\vars\Constants;

Requests::register_autoloader();

/**
 * Generates token to be used for authentication.
 * 
 * @param string    $secret_key
 */
function generateToken($secret_key = null)
{
    if (!empty($secret_key)) {
        $token = base64_encode(utf8_encode($secret_key));
        return $token;
    }
    throw new Exception('Secret key cannot be null.');
}

/**
 * Performs API calls.
 * 
 * @param string    $method
 * @param string    $endpoint
 * @param array     $headers
 * @param array     $json
 * @param array     $data
 */
function request($method, $endpoint, $headers = array(), $json = array(), $data = array())
{
    $url = Constants::BASE_URL . $endpoint;
    $payload = count($data) > 0 ? $data : json_encode($json);
    

    if ($method == 'POST') {
        $response = Requests::post($url, $headers, $payload);
    }

    if ($method == 'GET') {
        $response = Requests::get($url, $headers);
    }

    return $response;
}

/**
 * Validates user input based on type.
 * 
 * @param string    $key   key name
 * @param string    $value the user input value
 * @param string    $type  valid type of the value
 */
function validate($key, $value, $type) 
{
    if ($type === gettype($value)) {
        if (!empty($value)) {
            return $value;
        } else {
            if (gettype($value) === 'string') {
                throw new Exception("`{$key}` must be not be empty.");
            }
            if (gettype($value) === 'double') {
                throw new Exception("`{$key}` must be greater than `0.0`");
            }
        }
    }
    throw new Exception("`{$key}` must be of type `{$type}`");
}

/**
 * Maps keys of basis for validation over user input.
 * 
 * @param array $params     user input
 * @param array $keys       basis for validation
 */
function getValidatedPayload($params, $keys)
{
    $validatedPayload = array();
    if (is_array($params)) {
        foreach ($keys as $key => $value) {
            if ($value['required'] === true) {
                if (array_key_exists($key, $params)) {
                    // if key is required, add validated payload
                    $validatedPayload += [$key => validate($key, $params[$key], $value['type'])];
                } else {
                    throw new Exception("`{$key}` is required.`");
                }
            } else {
                if (array_key_exists($key, $params) && $params[$key] !== '') {
                    // if key is not required but is not empty, add validated payload
                    $validatedPayload += [$key => validate($key, $params[$key], $value['type'])];
                } else {
                    if (array_key_exists('default', $value)) {
                        // if key is not required and empty, if key has a default value, set to default
                        $validatedPayload += [$key => $value['default']];
                    } else {
                        // if key is not required and empty and has no default value, set to empty
                        $validatedPayload += [$key => ''];
                    }
                }
            }
        }
        return $validatedPayload;
    } else {
        throw new Exception("`$params` must be of type `array`");
    }
}

function encodeAdditionalData($additionalData)
{
    if (is_object($additionalData)) {
        return iconv('UTF-8', 'ASCII', base64_encode(utf8_encode(json_encode($additionalData))));
    }
    throw new Exception("'$additionalData must be of type `object`'");
}