<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Api;

class Client
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    const BASE_URL = '/store/api/v2/woocommerce';

    const STATUS_OK = 200;
    const STATUS_UNATHORIZED = 401;

    public function __construct(
        Config $config
    )
    {
        $this->config = $config;
        $this->host = $config->getBaseApiUrl();
    }

    const STATUS_CANT_CONNECT = 503;

    /**
     * @param string $method
     * @param string $action
     * @param array $params
     * @param array $data
     * @return array|false
     * @throws Exception
     */
    public function request($method, $action, $params = array(), $data = array()) {
        $params["version"] = $this->config->getVersion();
        $params["extID"] = $this->config->getExtID();
        $params["key"] = $this->config->getSecretKey();
        return $this->unprotectedRequest($method, $action, $params, $data);
    }

    /**
     * @param string $method
     * @param string $action
     * @param array $params
     * @param array $data
     * @return array|false
     * @throws Exception
     */
    public function unprotectedRequest($method, $action, $params = array(), $data = array())
    {
        $url = $this->host . self::BASE_URL . $action;
        $url = rtrim($url, '/');

        if (is_array($params) && count($params)) {
            $url .= '?' . http_build_query($params);
        }
        $curlHandle = curl_init();

        $headers = array(
            'Content-type: application/json',
        );

        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);

        set_time_limit(0);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 60 * 10);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 5);

        //Return the output instead of printing it
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_FAILONERROR, true);
        curl_setopt($curlHandle, CURLOPT_ENCODING, '');

        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);


        curl_setopt($curlHandle, CURLOPT_NOSIGNAL, 1);
        curl_setopt($curlHandle, CURLOPT_FAILONERROR, false);

        if (!$this->config->isValidateSSL()) {
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        }

        if ($method === self::METHOD_GET) {
            curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($curlHandle, CURLOPT_HTTPGET, true);
            curl_setopt($curlHandle, CURLOPT_POST, false);
        } elseif ($method === self::METHOD_POST) {
            $body = ($data) ? json_encode($data) : '';
            curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curlHandle, CURLOPT_POST, true);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $body);
        } elseif ($method === self::METHOD_DELETE) {
            curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($curlHandle, CURLOPT_POST, false);
        } elseif ($method === self::METHOD_PUT) {
            $body = ($data) ? json_encode($data) : '';
            curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $body);
            curl_setopt($curlHandle, CURLOPT_POST, true);
        }
        curl_exec($curlHandle);


        $httpStatus = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

        $response = curl_multi_getcontent($curlHandle);


        if ($httpStatus != self::STATUS_OK) {
            $error = curl_error($curlHandle);
            curl_close($curlHandle);
            $code = $httpStatus;
            if ($httpStatus == 0) {
                $code = self::STATUS_CANT_CONNECT;
                $response = "Connection error.";
            }
            throw new Exception("[$httpStatus]".$error."".$response, $code);
        }

        $result = json_decode($response, true);

        curl_close($curlHandle);

        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                $errorMsg = 'JSON parsing error: maximum stack depth exceeded';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $errorMsg = 'JSON parsing error: unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $errorMsg = 'JSON parsing error: syntax error, malformed JSON';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $errorMsg = 'JSON parsing error: underflow or the modes mismatch';
                break;
            case defined('JSON_ERROR_UTF8') ? JSON_ERROR_UTF8 : -1:
                $errorMsg = 'JSON parsing error: malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            case JSON_ERROR_NONE:
            default:
                $errorMsg = null;
                break;
        }
        if ($errorMsg !== null) {
            throw new \OmegaCommerce\Api\Exception(__($url . ' ' . $errorMsg . ' ' . $response));
        }

        return $result;
    }
}