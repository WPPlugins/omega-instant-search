<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */

namespace OmegaCommerce;

class Manager
{
    /**
     * @var Api\Indexer
     */
    protected $apiIndexer;

    /**
     * @var Model\Cron
     */
    protected $cron;

    public function __construct(
        $version
    )
    {
        $this->version = $version;
        $this->build();
    }

    public function build() {

        $config = new Model\Config($this->version);

        $apiConfig = new Api\Config($config);
        $apiClient = new Api\Client($apiConfig);
        $apiAuth = new Api\Auth($apiConfig, $apiClient);
        $apiIndexerHelper = new Api\Indexer\Helper();
        $apiBatch = new Api\Batch($apiClient, $apiIndexerHelper);
        $apiIndexer = new Api\Indexer($apiBatch, $apiClient);
        $apiIframe = new Api\Iframe($apiConfig, $apiAuth);

        $entityHelperRenderer = new Entity\Helper\Render();
        $entityHelperBlog = new Entity\Helper\Blog();
        $entityHelperPost = new Entity\Helper\Post();
        $entityHeplerProduct = new Entity\Helper\Product($entityHelperRenderer, $entityHelperPost);
        $entityHelperTerm = new Entity\Helper\Term();
        $entityHelperCategory = new Entity\Helper\Category($entityHelperTerm);
        $entityHelperBlogCategory = new Entity\Helper\BlogCategory($entityHelperTerm);
        $entityBlog = new Entity\Blog($entityHelperBlog);

        $entityPost = new Entity\Post($entityHelperPost);
        $entityPage = new Entity\Page($entityHelperPost);

        $entityBlogCategory = new Entity\BlogCategory($entityHelperBlogCategory);
        $entityPostTag = new Entity\PostTag();
        $apiIndexer->registerEntity($entityBlog);

        if (is_plugin_active("woocommerce/woocommerce.php")) {
            $entityAttribute = new Entity\Attribute();
            $apiIndexer->registerEntity($entityAttribute);

            $entityProduct = new Entity\Product($entityHeplerProduct, $entityHelperRenderer);

            $apiIndexer->registerEntity($entityProduct);
            $entityProductTag = new Entity\ProductTag();
            $apiIndexer->registerEntity($entityProductTag);

            $entityCategory = new Entity\Category($entityHelperCategory);
            $apiIndexer->registerEntity($entityCategory);
        }

        $apiIndexer->registerEntity($entityPost);
        $apiIndexer->registerEntity($entityPage);
        $apiIndexer->registerEntity($entityBlogCategory);
        $apiIndexer->registerEntity($entityPostTag);

        $syncController = new Controller\Admin\SyncController($apiConfig, $apiAuth, $apiIndexer);
        $applicationController = new Controller\Admin\ApplicationController($apiIframe);
        $settingController = new Controller\Admin\SettingController($entityHelperRenderer);
        $debugController = new Controller\Admin\DebugController($apiConfig, $apiAuth, $apiIndexer);


        new Model\Menu($syncController, $applicationController, $settingController, $debugController);
        new Model\Observer($apiIndexer);
        $this->cron = new Model\Cron($apiIndexer);
        new Block\Autocomplete($apiConfig);
        new Block\SearchResultsPage();

        $search = new Model\Module($apiAuth);

        $databaseMigration = new Model\DatabaseMigration();
        register_activation_hook(__FILE__, array($databaseMigration, 'install'));
        register_activation_hook(__FILE__, array($databaseMigration, 'installData'));

        if (is_plugin_active("enhanced-tooltipglossary/cm-tooltip-glossary.php")) {
            $entityPluginEnhancedTooltipglossary = new Entity\Plugins\EnhancedTooltipglossary($entityHelperPost);
            $apiIndexer->registerEntity($entityPluginEnhancedTooltipglossary);
        }

        $this->apiIndexer = $apiIndexer;
    }

    /**
     * @return Model\Cron
     */
    public function getCron() {
        return $this->cron;
    }

    /**
     * @return Api\Indexer
     */
    public function getApiIndexer() {
        return $this->apiIndexer;
    }
}

/**
 * Usage:
 * \OmegaCommerce\pr($entity, "x.x.x.x");
 *
 * @param mixed $ar
 * @param string $ip
 * @param bool $die
 * @return void
 */
function pr($ar, $ip, $die = false) {
    if ($ip != getIP()) {
        return;
    }
    echo "<pre>";
    print_r($ar);
    echo "</pre>";
    if ($die) {
        die;
    }
}

/**
 * @return string
 */
function getIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (!empty($_SERVER['HTTP_FORWARDED'])) {
        return $_SERVER['HTTP_FORWARDED'];
    }
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    return $_SERVER['REMOTE_ADDR'];
}

