<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Entity\Helper;

class Render
{
    const DELIMETER = "%X%Y%";
    const DELIMETER2 = "%%XX%YY%%";

    /**
     *  Test handler
     *  /wp-admin/admin-ajax.php?action=omega_render_test&product_id=136
     */

    public function __construct()
    {
        add_action('wp_ajax_omega_render_test', array(&$this, 'omegaRenderTest'));
        add_action('wp_ajax_nopriv_omega_render_test', array(&$this, 'omegaRenderTest'));

        add_action('wp_ajax_omega_render', array(&$this, 'omegaRender'));
        add_action('wp_ajax_nopriv_omega_render', array(&$this, 'omegaRender'));
    }

    function getProductIds()
    {
        if (isset($_GET['ids'])) {
            $productIds = explode(",", $_GET['ids']);
        } else {
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 1,
                'status' => 'published',
            );
            $wp_query = new \WP_Query($args);
            $posts = $wp_query->get_posts();

            $productIds = array(current($posts)->ID);
        }
        return $productIds;
    }

    function omegaRenderTest()
    {
        if (isset($_GET['d'])) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

        $productIds = $this->getProductIds();
        foreach ($this->renderProductBlocksCall($productIds) as $id => $html) {
            echo $html;
        }
        die;
    }


    function omegaRender()
    {
        if (isset($_GET['d'])) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

        $productIds = $this->getProductIds();
        echo $this->renderProductBlocks($productIds);
        die;
    }


    public function request($productIds)
    {
        $host = parse_url(get_site_url(), PHP_URL_HOST);
        $addr = parse_url(get_site_url(), PHP_URL_HOST);
        if (isset($_SERVER["SERVER_ADDR"])) {
            $addr = $_SERVER["SERVER_ADDR"];
        }
        $protocol = parse_url(get_site_url(), PHP_URL_SCHEME);
        if ($protocol == "https") {
            $addr = $host;
        }

        $url = admin_url("admin-ajax.php");
        $path = parse_url($url, PHP_URL_PATH);
        $url = $protocol . "://" . $addr . $path."?action=omega_render&context=frontend&ids=" . implode(",", $productIds);

        $ch = curl_init();
        set_time_limit(0);

        $headers = array("Host: $host");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_TIMEOUT, 60 * 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //Return the output instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');

        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_exec($ch);
        $httpStatus = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response = curl_multi_getcontent($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error != "" || $httpStatus != 200) {
            if (isset($_GET['d'])) {
                echo $error;
            }
            return $this->renderProductBlocks($productIds);
        }
        return $response;
    }


    public function renderProductBlocksCall(array $productIds)
    {
        if (!count($productIds)) {
            return array();
        }
        $response = $this->request($productIds);
        $blocks = array();
        $parts = explode(self::DELIMETER, $response);
        foreach ($parts as $part) {
            $p = explode(self:: DELIMETER2, $part);
            $blocks[$p[0]] = $p[1];
        }
        return $blocks; // id => html
    }


    public function renderProductBlocks(array $productIds)
    {
        global $wp_query; //necessary for correct render!

        $_SERVER['REQUEST_URI'] = "";
        $GLOBALS['current_screen'] = $this;

        ob_start();
        woocommerce_product_loop_start();
        ob_get_contents();
        ob_end_clean();

        $args = array(
            'post_type' => 'product',
            'post__in' => $productIds,
            'posts_per_page' => 100,
        );
        $wp_query = new \WP_Query($args);
        $wp_query->is_search = true;//to display products with visability = 'search'
        $posts = $wp_query->get_posts();
        $results = array();
        foreach ($posts as $post) {
            the_post();
            ob_start();
            $productTemplatePartContent = 'content';
            if (defined("OMEGA_SEARCH_PRODUCT_TEMPLATE_PART_CONTENT")) {
                $productTemplatePartContent = OMEGA_SEARCH_PRODUCT_TEMPLATE_PART_CONTENT;
            }
            $productTemplatePartSlug = 'product';
            if (defined("OMEGA_SEARCH_PRODUCT_TEMPLATE_PART_SLUG")) {
                $productTemplatePartSlug = OMEGA_SEARCH_PRODUCT_TEMPLATE_PART_SLUG;
            }
            wc_get_template_part($productTemplatePartContent, $productTemplatePartSlug);
            $results[] = $post->ID . self::DELIMETER2 . ob_get_contents();
            ob_end_clean();
        }
        return implode(self::DELIMETER, $results);

    }


    /**
     * Here we overwrite wp function
     * @return bool
     */
    public function in_admin()
    {
        return false;
    }

}