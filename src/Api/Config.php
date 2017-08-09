<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */

namespace OmegaCommerce\Api;

class Config
{
    public function __construct(
        \OmegaCommerce\Api\Interfaces\ConfigInterface $config
    )
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->config->getVersion();
    }

    /**
     * @return string
     */
    public function getExtID()
    {
        return $this->config->getValue('omega_api/access/ext_id');
    }

    /**
     * @param string $value
     * @return void
     */
    public function setExtID($value)
    {
        $this->config->saveValue('omega_api/access/ext_id', $value);
    }

    /**
     * @return string
     */
    public function getID()
    {
        return $this->config->getValue('omega_api/access/id');
    }


    /**
     * @param string $value
     * @return void
     */
    public function setID($value)
    {
        $this->config->saveValue('omega_api/access/id', $value);

    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->config->getEncryptedValue('omega_api/access/secret_key');
    }


    /**
     * @param string $value
     * @return void
     */
    public function setSecretKey($value)
    {
        $this->config->saveValueEncrypted('omega_api/access/secret_key', $value);

    }

    /**
     * @return string
     */
    public function getBaseApiUrl()
    {
        $url = $this->config->getValue('omega_api/access/base_url');
        if ($url == "") { //if plugin enables first time, we have empty url. need additional checks.
            $url = "https://search.omegacommerce.com";
        }
        return rtrim($url, "/");
    }

    /**
     * @return bool
     */
    public function isValidateSSL()
    {
        if ($this->config->getValue('omega_api/access/is_validate_ssl') === "") {//true by default
            return true;
        }
        return $this->config->getValue('omega_api/access/is_validate_ssl');
    }
}
