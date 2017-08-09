<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */

namespace OmegaCommerce\Entity\Helper;

class Post
{
    public function wpmlAddData($post, array $data) {
        if (function_exists("wpml_get_language_information")) {
            $post_language_information = wpml_get_language_information(null, $post->ID);
            $data["languageCode"] = $post_language_information["language_code"];
        }
        return $data;
    }


    /**
     * @param \WP_Post $post
     * @return array
     */
    public function getData($post) {
        $data = array();
        $data['post_id'] = $post->ID;
        $data['store_id'] = get_current_blog_id();
        $data['category_ids'] = $this->get_category_ids($post->ID);
        $data['image_url'] = $this->get_image($post);
        $data['small_image_url'] = $this->get_image($post, 'medium');
        $data['thumbnail_url'] = $this->get_image($post, 'thumbnail');
        $data['url'] = get_permalink($post);
        if (strpos($post->post_content, "omega_search_results") === false) {
            $data['content'] = apply_filters('the_content', $post->post_content);
            $data['description'] = $data['content'];  //for items, that extend this class
        }
        $data['excerpt'] = apply_filters('the_excerpt', $post->post_excerpt);
        $data['post_date'] = $post->post_date;
        $data['post_date_gmt'] = $post->post_date_gmt;
        $data['post_modified'] = $post->post_modified;
        $data['post_modified_gmt'] = $post->post_modified_gmt;
        $data['post_title'] = $post->post_title;
        $data['handle'] = $post->post_name;
        $data['status'] = $post->post_status;
        $data['tags'] = wp_get_post_terms($post->ID, 'post_tag', array('fields' => 'names'));
        $data = $this->wpmlAddData($post, $data);

        //for items, that extend this class
        $data['id'] = $post->ID;
        $data['is_active'] = $post->post_status == "publish";
        $data['short_description'] = $data['excerpt'];
        $data['name'] = $data['post_title'];
        return $data;
    }

    /**
     * @param int $post_id
     * @return array
     */
    private function get_category_ids($post_id)
    {
        $categories = get_the_terms($post_id, 'product_cat');

        $return = array();
        if ($categories) {
            foreach ($categories as $category) {
                $return[] = $category->term_id;
            }
        }

        return $return;
    }

    /**
     * Returns the main product image.
     *
     * @param \WP_Post $post
     * @param string $size (default: 'shop_thumbnail')
     * @return string
     */
    private function get_image($post, $size = 'shop_thumbnail')
    {
        if (has_post_thumbnail($post->ID)) {
            $image = $this->get_the_post_thumbnail_url($post->ID, $size);
        } elseif (($parent_id = wp_get_post_parent_id($post->ID)) && has_post_thumbnail($parent_id)) {
            $image = $this->get_the_post_thumbnail_url($parent_id, $size);
        } else {
            $image = '';
        }

        return $image;
    }

    /**
     * @param int $post_id
     * @param string $size
     * @return bool|false|string
     */
    private function get_the_post_thumbnail_url($post_id, $size)
    {
        if (function_exists('get_the_post_thumbnail_url')) {
            return get_the_post_thumbnail_url($post_id, $size);
        } else {
            $post_thumbnail_id = get_post_thumbnail_id($post_id);
            if (!$post_thumbnail_id) {
                return false;
            }
            $image = wp_get_attachment_image_src($post_thumbnail_id, $size);
            return isset($image['0']) ? $image['0'] : false;
        }
    }
}
