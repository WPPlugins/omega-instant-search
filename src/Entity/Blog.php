<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Entity;

use OmegaCommerce\Api\Entity\Table;
use OmegaCommerce\Api\Interfaces\EntityInterface;

class Blog implements EntityInterface
{

    public function __construct(
        \OmegaCommerce\Entity\Helper\Blog $blogHelper
    )
    {
        $this->blogHelper = $blogHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getMainTable() {
        global $wpdb;
        if (is_multisite()) {
            $table = new Table($wpdb->prefix."blogs");
            $table->addIDField("blog_id");
            $table->addField("domain");
            $table->addField("path");
            return $table;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkedTables() {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getType() {
        return "store";
    }

    /**
     * {@inheritdoc}
     */
    public function getHumanName() {
        return "blogs";
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByIds($ids){
        if (!is_multisite()) {
            $item = $this->blogHelper->getData(get_current_blog_id());
            return array(
                $item
            );
        }
        $sites = get_sites();
        $items = array();
        foreach($sites as $site) {
            $items[] = $this->blogHelper->getData($site->blog_id);
        }
        return $items;
    }
}

