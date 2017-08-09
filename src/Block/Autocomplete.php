<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Block;

class Autocomplete
{

    public function __construct(
        \OmegaCommerce\Api\Config $config
    )
    {
        add_action('wp_head', array(&$this, 'autocomplete'));
        $this->config = $config;
        $this->storeId = get_current_blog_id();
    }

    /**
     * @return void
     */
    public function autocomplete()
    {
        echo $this->toHtml();
        include WP_OMEGA_COMMERCE_PLUGIN_PATH . "src/view/templates/searchForm.php";
        if ($css = get_option('omega_search_custom_css')) {
            echo "<style>$css</style>";
        }
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $id = $this->config->getID();
        if (!$id) {
            return "";
        }
        $url = $this->config->getBaseApiUrl();
        $url = str_replace('https://', '', str_replace('http://', '', $url));
        $url = rtrim($url, '/');
        $page = $this->getSearchResultPage();
        $resultsUrl = get_permalink($page);
        return <<<HTML
<script data-cfasync="false" src="//{$url}/instant/initjs?ID={$id}&seid={$this->storeId}"></script>
<script>'' +
    (function () {
        var endpoint = '{$url}';
        var protocol= ("https:" === document.location.protocol ? "https://" : "http://");
        //url must have the same protocol as page. otherwise js errors possible.
        var url = '{$resultsUrl}'
        url = url.replace("https://", protocol)
        url = url.replace("http://", protocol)
        if (typeof window.OMEGA_CONFIG == "undefined") {
            window.OMEGA_CONFIG = {}
        }
        window.OMEGA_CONFIG.searchResultUrl = url
    })();
</script>
HTML;

    }

    /**
     * @return \WP_Post
     */
    public function getSearchResultPage()
    {
        $slug = 'omega-search';
        $page = get_page_by_path($slug, OBJECT, 'page');
        if ($page) {
            return $page;
        }
        wp_insert_post(array(
            'post_title' => __('Search Results'),
            'post_type' => 'page',
            'post_content' => '[omega_search_results]',
            'post_name' => $slug,
            'post_status' => 'publish',
        ));
        return get_page_by_path($slug, OBJECT, 'page');
    }
}