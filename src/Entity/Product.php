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

class Product implements EntityInterface
{
    public function __construct(
        \OmegaCommerce\Entity\Helper\Product $productHelper,
        \OmegaCommerce\Entity\Helper\Render $renderHelper
    )
    {
        $this->productHelper = $productHelper;
        $this->renderHelper = $renderHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getMainTable() {
        global $wpdb;

        $table = new Table($wpdb->prefix."posts");
        $table->addIDField("ID");
        $table->addField("post_title");
        $table->addField("post_content");
        $table->addField("post_date_gmt");
        $table->setWhere("main.post_status = 'publish' AND main.post_type = 'product'");
        return $table;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkedTables() {
        global $wpdb;

        $table = new Table($wpdb->prefix."postmeta");
        $table->addIDField("post_id");
        $table->addField("meta_key");
        $table->addField("meta_value");
        $table->addLeftJoin($wpdb->prefix."posts pp ON main.post_id = pp.ID");
        $table->setWhere("pp.post_status = 'publish' AND pp.post_type = 'product'");
        return array($table);
    }


    /**
     * {@inheritdoc}
     */
    public function getType() {
        return "product";
    }

    /**
     * {@inheritdoc}
     */
    public function getHumanName() {
        return "products";
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByIds($ids){
        global $wp_query; //necessary for correct render!
        $args = array(
            'post_type' => 'product',
            'post__in' => $ids,
            'posts_per_page' => count($ids),
        );
        $wp_query = new \WP_Query($args);
        $posts = $wp_query->get_posts();

        $items = array();
        foreach($posts as $post) {
            $items[$post->ID] = $this->productHelper->getData($post);
        }
        if (get_option(\OmegaCommerce\Model\Config::IS_ENABLED_TEMPLATE_RENDERING)) {
            $blocks = $this->renderHelper->renderProductBlocksCall($ids);
            foreach ($blocks as $id => $html) {
                if (isset($items[$id])) {
                    $items[$id]['blocks']['productcard'] = $html;
                }
            }
        }

        return $items;
    }
}

