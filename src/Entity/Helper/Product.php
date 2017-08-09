<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */

namespace OmegaCommerce\Entity\Helper;

class Product
{
    public function __construct(
        \OmegaCommerce\Entity\Helper\Render $renderHelper,
        \OmegaCommerce\Entity\Helper\Post $postHelper
    )
    {
        $this->renderHelper = $renderHelper;
        $this->postHelper = $postHelper;
    }


    /**
     * @param \WP_Post $post
     * @return array
     */
    public function getData($post) {
        $data = $this->postHelper->getData($post);
        $product = wc_get_product($post->ID);
        $data['sku'] = $product->get_sku();
        $data['final_price'] = $product->get_sale_price();
        $data['price'] = $this->getProductDisplayPrice($product);
        $attributes = $this->getAttributes($product);
        if (count($attributes)) { //if empty, our backend fails with error
            $data['attributes'] = $attributes;
        }
        if ($this->getProductType($product) == 'variable') {
            $variations = $this->get_variations($product);
            if (count($variations)) {
                $data['variants'] = $variations;
            }
        }
        $data['store_id'] = get_current_blog_id();

        return $data;
    }

    /**
     * @param \WC_Product $product
     * @return string
     */
    private function getProductType($product) {
        if (method_exists($product, "get_type")) {
            return $product->get_type();
        }
        return $product->product_type;
    }

    /**
     * @param \WC_Product $product
     * @return float
     */
    private function getProductDisplayPrice($product)
    {
        if (function_exists("wc_get_price_to_display")) {//woo 3.0
            return wc_get_price_to_display($product);
        }

        if (method_exists($product, 'get_display_price')) {
            return $product->get_display_price();
        }

        $taxDisplayMode = get_option('woocommerce_tax_display_shop');

        return $taxDisplayMode == 'incl' ? $product->get_price_including_tax() : $product->get_price_excluding_tax();
    }


    /**
     * @param \WC_Product $product
     * @return array
     */
    private function get_variations($product)
    {
        $variations = $product->get_available_variations();
        $data = array();
        foreach ($variations as $variation) {
            $item = array(
                'variation_id' => $variation['variation_id'],
                'sku' => $variation['sku'],
                'is_in_stock' => $variation['is_in_stock'],
                'description' => $variation['variation_description'],
                'v2_price' => $variation['price'],
                'regular_price' => $variation['regular_price'],
                'sale_price' => $variation['sale_price'],
                'stock_quantity' => $variation['stock_quantity'],
            );
            if (isset($variation['display_price'])) {
                $item['v2_price'] = $variation['display_price'];
            }
            if (isset($variation['display_regular_price'])) {
                $item['regular_price'] = $variation['display_regular_price'];
            }
            if (count($variation['attributes'])) { //if empty, our backend fails with error
                $item['attributes'] = $variation['attributes'];
            }
            $data[] = $item;
        }

        return $data;
    }

    /**
     * @param \WC_Product $product
     * @return array
     */
    private function getAttributes($product)
    {
        if (!defined('WC_DELIMITER')) {
            $delimiter = '|';
        } else {
            $delimiter = WC_DELIMITER;
        }
        $attributes = $product->get_attributes();
        $data = array();
        foreach ($attributes as $attribute) {
            $code = $attribute['name'];
            if ($attribute['is_taxonomy']) {
                $value = wc_get_product_terms($product->get_id(), $code, array('fields' => 'names'));
            } else {
                $value = explode($delimiter, $attribute['value']);
            }
            $data[$code] = $value;
        }
        return $data;
    }
}
