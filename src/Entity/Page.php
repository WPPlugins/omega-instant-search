<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Entity;

class Page extends Post
{
    /**
     * {@inheritdoc}
     */
    public function getType() {
        return "page";
    }

    /**
     * {@inheritdoc}
     */
    public function getHumanName() {
        return "pages";
    }

    /**
     * @return string
     */
    public function getPostType() {
        return "page";
    }

}

