<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Model;

class DatabaseMigration
{

    const DB_SCHEMA_VERSION = '1.1';
    const DB_DATA_VERSION = '1.1';
    const OMEGA_SYNC_STATUS_TABLE = 'omega_index_status';



    public function __construct()
    {
        add_action('plugins_loaded', array(&$this, 'updateDBCheck'));
    }

    public function updateDBCheck()
    {
        $version = get_site_option('omega_core_db_version');
        if ($version != self::DB_SCHEMA_VERSION) {
            $this->install($version);
        }

        $version = get_site_option('omega_core_db_data_version');
        if ($version != self::DB_DATA_VERSION) {
            $this->installData($version);
        }
    }

    public function install($oldVersion)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::OMEGA_SYNC_STATUS_TABLE;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE `$table_name` (
  `entity_id` bigint(20) NOT NULL,
  `entity_type` varchar(25) NOT NULL,
  `table_name` varchar(25) NOT NULL DEFAULT '',
  `updated_at` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `hash` TEXT DEFAULT NULL,
  `old_hash` TEXT DEFAULT NULL,
  `is_reindexed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`entity_id`,`entity_type`,`table_name`)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        add_option('omega_core_db_version', self::DB_SCHEMA_VERSION);
    }

    public function installData($oldVersion)
    {
        if ($oldVersion == "" && get_option("omega_api/is_synced")) {
            $oldVersion = "1.0";
            add_option(Config::IS_ENABLED_TEMPLATE_RENDERING, true);
        }

        if ($oldVersion == "1.0") {
            add_option(Config::NOTICE_FLAG_ASK_REINDEX, true);
        }

        add_option('omega_core_db_data_version', self::DB_DATA_VERSION);
    }
}