<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */

namespace OmegaCommerce\Entity\Helper;

class Blog
{

    public function getData($blogId)
    {
        $this->switchToBlog($blogId);
        $data = array();
        $data['blog_id'] = $blogId;
        $data['name'] = get_option('blogname');
        $data['url'] = get_option('siteurl');
        $data['is_active'] = true;
        $data['currency'] = $this->getBlogOption($blogId, 'woocommerce_currency', '');
        $data['locale'] = get_locale();
        $data['currency_format'] = $this->getWoocommercePriceFormat();
        $this->restoreCurrentBlog();
        return $data;
    }

    /**
     * @return string
     */
    private function getWoocommercePriceFormat()
    {
        $currency = get_option('woocommerce_currency', '');
        if (!function_exists('get_woocommerce_currency_symbol')) {
            return '';
        }
        $currency_pos = get_option('woocommerce_currency_pos');
        $decimal_sep = get_option('woocommerce_price_decimal_sep');
        $format = '%1$s%2$s';

        switch ($currency_pos) {
            case 'left' :
                $format = '%1$s%2$s';
                break;
            case 'right' :
                $format = '%2$s%1$s';
                break;
            case 'left_space' :
                $format = '%1$s&nbsp;%2$s';
                break;
            case 'right_space' :
                $format = '%2$s&nbsp;%1$s';
                break;
        }

        $currency_pos = apply_filters('woocommerce_price_format', $format, $currency_pos);
        $currency_symbol = get_woocommerce_currency_symbol($currency);

        return str_replace(array('%1$s', '%2$s'), array($currency_symbol, '0' . $decimal_sep . '00'), $currency_pos);
    }

    private function getBlogOption($id, $option, $default = false)
    {
        if (function_exists("getBlogOption")) {
            return getBlogOption($id, $option, $default);
        }
        return get_option($option, $default);
    }

    private function restoreCurrentBlog()
    {
        if (function_exists("restoreCurrentBlog")) {
            return restoreCurrentBlog();
        }
    }

    private function switchToBlog($id)
    {
        if (function_exists("switchToBlog")) {
            return switchToBlog($id);
        }
    }
}
