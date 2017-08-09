<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */

namespace OmegaCommerce\Api;

class Auth
{

    protected $token;

    public function __construct(
        Config $config,
        Client $client
    )
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * @return bool
     */
    public function isAuthorized()
    {
        return $this->config->getSecretKey() != "" && $this->config->getID() != "";
    }

    /**
     * @return string
     */
    public function getExtId()
    {
        if ($extId = $this->config->getExtID()) {
            return $extId;
        }

        $data = array();
        $data['platform'] = "woocommerce";
        $data['domain'] = $_SERVER['HTTP_HOST'];
        $extId = md5($data['domain'] . $data['platform'] . time());
        $this->config->setExtID($extId);

        return $extId;
    }

    /**
     * @param string $siteURL
     * @return array
     */
    public function register($siteURL)
    {
        $secretKey = bin2hex(openssl_random_pseudo_bytes(20));
        $data = $this->getRegistrationData($siteURL, $secretKey);
        $data = $this->client->request(Client::METHOD_POST, "/auth/register", array(), $data);
        $this->config->setSecretKey($secretKey);
        $this->config->setID($data["ID"]);
        return $data;
    }

    /**
     * @return array|false
     */
    public function remove()
    {
        try {
            return $this->client->request(Client::METHOD_POST, "/auth/remove");
        } catch (Exception $e) {
        }
    }

    /**
     * @param string $siteURL
     * @param string $secretKey
     * @return array
     */
    public function getRegistrationData($siteURL, $secretKey)
    {

        $data = array(
            'extID' => $this->getExtId(),
            'secretKey' => $secretKey,
            'product' => "search",
            'domain' => $_SERVER['HTTP_HOST'],
            'URL' => $siteURL,
            'platform' => "woocommerce",
        );

        return $data;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        if (!$this->token) {
            $token = $this->client->unprotectedRequest("POST", $this->getAuthUrl());
            if (!$token) {
                return false;
            }
            $token = $token["token"];
            $this->token = $token;
        }
        return $this->token;
    }

    /**
     * @param array $data
     * @return string
     */
    public function getAuthUrl($data = array())
    {
        $data['timestamp'] = time();
        $data2 = array(
            "v" => $this->config->getVersion(),
            "extID" => $this->getExtID(),
        );
        $data = array_merge($data, $data2);
        ksort($data);
        $url = array();
        foreach ($data as $k => $v) {
            $url[] = "$k=$v";
        }
        $url = implode("&", $url);
        $hmac = hash_hmac('sha256', $url, $this->config->getSecretKey());
        return "/auth/token?$url&hmac=$hmac";
    }
}