<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */

namespace OmegaCommerce\Entity\Helper;

class Category
{
    public function __construct(
        \OmegaCommerce\Entity\Helper\Term $termHelper
    )
    {
        $this->termHelper = $termHelper;
    }

    /**
     * @param \WP_Term $category
     * @return array
     */
    public function getData($category) {
        $data = $this->termHelper->getData($category);

        $thumbnail_id = get_woocommerce_term_meta($data['term_id'], 'thumbnail_id', true);
        $image = wp_get_attachment_url($thumbnail_id);
        if ($image) {
            $data['img_url'] = $image;
        }
        $data['url'] = get_term_link($category->term_id, 'product_cat');
        return $data;
    }
}
