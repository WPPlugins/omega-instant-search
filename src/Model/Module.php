<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Model;

class Module
{
    const REDIRECT_OPTION = "omega_search_do_activation_redirect";

    /**
     * Construct the plugin object
     */
    public function __construct(
        \OmegaCommerce\Api\Auth $auth
    )
    {
        $this->auth = $auth;
        add_action('admin_init', array(&$this, 'initSettings'));

        register_activation_hook(OMEGA_COMMERCE_SEARCH_FILE, array(&$this, 'activate'));
        register_deactivation_hook(OMEGA_COMMERCE_SEARCH_FILE, array(&$this, 'deactivate'));

        $this->checkForRedirect();
    }

    /**
     * Initialize some custom settings
     */
    public function initSettings()
    {
        register_setting('omega_core-group', 'omega_api_access_iuid');
        register_setting('omega_core-group', 'omega_api_access_secret_key');
        register_setting('omega_search-group', 'omega_api_sync_mode');
        register_setting('omega_search-group', 'omega_api_sync_time');
        register_setting('omega_search-group', 'omega_api_sync_allowed');
        register_setting('omega_search-group', 'omega_api_max_sync_number');
        register_setting('omega_search-group', 'omega_api_access_base_url');
        register_setting('omega_search-group', 'omega_search_box_selector');
        register_setting('omega_search-group', 'omega_search_custom_css');
        register_setting('omega_search-group', 'omega_api_access_is_validate_ssl');
        add_option('omega_api_sync_mode', Config::REINDEX_AFTER_SAVE);
        add_option('omega_api_max_sync_number', 30);
        add_option('omega_api_access_base_url', "https://search.omegacommerce.com");
        add_option('omega_api_access_is_validate_ssl', true);


        if (current_user_can('manage_options')) {
            add_action('admin_notices', array(&$this, 'addNotices'));
        }
    }

    /**
     * @return void
     */
    private function checkForRedirect() {
        if (get_option(self::REDIRECT_OPTION) == "activate") {
            delete_option(self::REDIRECT_OPTION);
            header("Location: admin.php?page=omega_commerce_sync_page&activation=1", true, 302);
            die;
        }
    }

    /**
     * Activate plugin
     */
    public function activate()
    {
        $this->auth->register(get_site_url());
        add_option(self::REDIRECT_OPTION, "activate");
    }

    /**
     * Deactivate the plugin
     */
    public function deactivate()
    {
        $this->auth->remove();
    }

    public function addNotices()
    {
        if (get_option(Config::NOTICE_FLAG_ASK_REINDEX)) {
            echo '<div id="message" class="updated">
	<p>
	Thanks for updating to the new version of Omega Search. This update requires <a href="admin.php?page=omega_commerce_sync_page">to run full data reindexing</a>.
    </p>
</div>
';
        }
    }
}