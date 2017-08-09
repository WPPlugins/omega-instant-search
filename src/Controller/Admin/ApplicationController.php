<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Controller\Admin;


class ApplicationController
{
    public function __construct(
        \OmegaCommerce\Api\Iframe $iframe
    )
    {
        $this->iframe = $iframe;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            echo $this->iframe->toHtml();
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            echo $error_message;
        }
    }
}