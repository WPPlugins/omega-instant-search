<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Model;

class Menu
{
    const OMEGA_COMMERCE_CAPABILITY = 'manage_options';

    /**
     * Construct the plugin object
     */
    public function __construct(
        \OmegaCommerce\Controller\Admin\SyncController $syncController,
        \OmegaCommerce\Controller\Admin\ApplicationController $applicationController,
        \OmegaCommerce\Controller\Admin\SettingController $settingController,
        \OmegaCommerce\Controller\Admin\DebugController $debugController
    )
    {
        $this->syncController = $syncController;
        $this->debugController = $debugController;
        $this->applicationController = $applicationController;
        $this->settingController = $settingController;

        add_action('admin_menu', array(&$this, 'addMenu'));
        add_action('update_option_omega_api_access_roles', array(&$this, 'addCaps'));
//        add_filter('plugin_action_links_' . plugin_basename(wp_normalize_path(OMEGA_COMMERCE_SEARCH_FILE)), array($this, 'insertPluginLinks'), 10, 4);


        // added WP wrapper to omega admin page
        add_filter('contextual_help_list', array(&$this, 'startWrapper'));
        add_action('toplevel_page_omega_commerce', array(&$this, 'endWrapper'));

        add_action('admin_head', array(&$this, 'addCustomCSS'));
    }

    /**
     * Start WP wrapper for omega admin page
     */
    public function startWrapper($help)
    {
        echo '<div class="wrap">';
        return $help;
    }
    /**
     * End WP wrapper for omega admin page
     */
    public function endWrapper()
    {
        echo '</div>';

    }


    /**
     * Custom css
     */
    public function addCustomCSS()
    {
        echo '
            <style>
                .toplevel_page_omega_commerce img {
                    width: 20px;
                    height: 20px;
                }
            </style>';
    }

    /**
     * Add a menu
     */
    public function addMenu()
    {
        add_menu_page(
            __('Search Dashboard'),
            __('Omega Search'),
            self::OMEGA_COMMERCE_CAPABILITY,
            'omega_commerce',
            array(&$this->applicationController, 'execute'),
            sprintf("%ssrc/view/images/logo.png", WP_OMEGA_COMMERCE_PLUGIN_URL)
        );

        add_submenu_page(
            'omega_commerce',
            __('Omega Commerce Sync'),
            __('Data Reindexing'),
            self::OMEGA_COMMERCE_CAPABILITY,
            'omega_commerce_sync_page',
            array(&$this->syncController, 'showButtonAction')
        );
        add_submenu_page(
            'omega_commerce',
            __('Settings'),
            __('Settings'),
            self::OMEGA_COMMERCE_CAPABILITY,
            'omega_commerce_settings',
            array(&$this->settingController, 'showAction')
        );
        add_submenu_page(
            null, //we don't want to add it to menu
            __('Omega Commerce Sync'),
            __('Sync'),
            self::OMEGA_COMMERCE_CAPABILITY,
            'omega_commerce_sync',
            array(&$this->syncController, 'showAction')
        );
        add_submenu_page(
            null, //we don't want to add it to menu
            __('Omega Commerce Debug'),
            __('Sync'),
            self::OMEGA_COMMERCE_CAPABILITY,
            'omega_commerce_debug',
            array(&$this->debugController, 'showAction')
        );
    }

    /**
     * Insert links in Plugin setup page
     */
    public function insertPluginLinks($links, $file, $plugin_data, $context)
    {
        $link1 = '<a href="admin.php?page=omega_commerce">Dashboard</a>';
        $link3 = '<a href="admin.php?page=omega_commerce_settings">Settings</a>';
        array_unshift($links, $link3);
        array_unshift($links, $link1);
        return $links;
    }

    /**
     * ACL
     */
    public function addCaps()
    {
        $role_class = wp_roles();
        // get role names
        $roles = $role_class->get_names();
        $allowed_roles = get_option('omega_api_access_roles', array());
        foreach ($roles as $name => $v) {
            $role = get_role($name);
            if (in_array($name, $allowed_roles)) {
                $role->add_cap(self::OMEGA_COMMERCE_CAPABILITY);
            } else {
                $role->remove_cap(self::OMEGA_COMMERCE_CAPABILITY);
            }
        }
    }

}