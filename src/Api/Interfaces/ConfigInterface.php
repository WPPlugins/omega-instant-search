<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */

namespace OmegaCommerce\Api\Interfaces;


interface ConfigInterface
{
    /**
     * @return string
     */
    function getVersion();

    /**
     * @param string $path
     * @return string
     */
    function getValue($path);

    /**
     * @param string $path
     * @param string $value
     * @return void
     */
    function saveValue($path, $value);


    /**
     * @param string $path
     * @return string
     */
    function getEncryptedValue($path);

    /**
     * @param string $path
     * @param string $value
     * @return void
     */
    function saveValueEncrypted($path, $value);
}