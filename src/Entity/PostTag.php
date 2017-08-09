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

class PostTag extends TermAbstract implements EntityInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTaxonomyType() {
        return "post_tag";
    }


    /**
     * {@inheritdoc}
     */
    public function getType() {
        return "post_tags";
    }

    /**
     * {@inheritdoc}
     */
    public function getHumanName() {
        return "post tags";
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByIds($ids){
        $terms = get_terms(array(
            'taxonomy' => $this->getTaxonomyType(),
            'hide_empty' => false,
            'include' => $ids,
        ));
        $items = array();
        foreach($terms as $term) {
            $data = array();
            $data['id'] = $term->term_id;
            $data['name'] = $term->name;
            $data['url'] = site_url().'/tag/'.$term->slug;
            $data["is_active"] = true;
            $data['store_id'] = get_current_blog_id();
            $items[] = $data;
        }
        return $items;
    }
}

