<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Entity\Plugins;


class EnhancedTooltipglossary extends \OmegaCommerce\Entity\Post
{
    /**
     * {@inheritdoc}
     */
    public function getType() {
        return "glossary_items";
    }

    /**
     * {@inheritdoc}
     */
    public function getHumanName() {
        return "glossary items";
    }

    /**
     * {@inheritdoc}
     */
    public function getPostType() {
        return "glossary";
    }
}

