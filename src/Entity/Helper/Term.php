<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */

namespace OmegaCommerce\Entity\Helper;

class Term
{
    /**
     * @param \WP_Term $term
     * @return array
     */
    public function getData($term) {
        $data = array();
        $data['term_id'] = $term->term_id;
        $data['name'] = $term->name;
        $data['slug'] = $term->slug;
        $data['count'] = $term->count;
        $data['store_id'] = get_current_blog_id();
        return $data;
    }
}
