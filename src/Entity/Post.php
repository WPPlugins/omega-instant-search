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

class Post implements EntityInterface
{

    public function __construct(
        \OmegaCommerce\Entity\Helper\Post $postHelper
    )
    {
        $this->postHelper = $postHelper;
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
        $table->setWhere("main.post_status = 'publish' AND main.post_type = '{$this->getPostType()}'");
        return $table;
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
        return "post";
    }

    /**
     * @return string
     */
    public function getPostType() {
        return "post";
    }

    /**
     * {@inheritdoc}
     */
    public function getHumanName() {
        return "posts";
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByIds($ids){
        global $wp_query;
        $args = array(
            'post_type' => $this->getPostType(),
            'post__in' => $ids,
            'posts_per_page' => count($ids),
        );
        $wp_query = new \WP_Query($args);
        $posts = $wp_query->get_posts();

        $items = array();
        foreach($posts as $post) {
            $data = $this->postHelper->getData($post);
            $data['tags'] = implode(",", $data['tags']);
            $items[] = $data;
        }
        return $items;
    }
}

