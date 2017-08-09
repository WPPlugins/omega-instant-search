<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */

namespace OmegaCommerce\Model;

class Config implements \OmegaCommerce\Api\Interfaces\ConfigInterface
{
    const REINDEX_BY_CRON = 1;
    const REINDEX_AFTER_SAVE = 0;

    const NOTICE_FLAG_ASK_REINDEX = "omega_notice_ask_reindex";
    const IS_ENABLED_TEMPLATE_RENDERING = "omega_is_enabled_template_rendering";

    public function __construct(
        $version
    )
    {
        $this->version = $version;
    }


    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    function getValue($path)
    {
        return get_option($this->convert_to_wp_style($path), '');
    }

    /**
     * {@inheritdoc}
     */
    function saveValue($path, $value)
    {
        update_option($this->convert_to_wp_style($path), $value);
    }

    /**
     * {@inheritdoc}
     */
    function getEncryptedValue($path)
    {
        return get_option($this->convert_to_wp_style($path), '');
    }

    /**
     * {@inheritdoc}
     */
    function saveValueEncrypted($path, $value)
    {
        update_option($this->convert_to_wp_style($path), $value);
    }

    /**
     * @param string $path
     * @return string
     */
    private function convert_to_wp_style($path)
    {
        return str_replace('/', '_', $path);
    }
}
