<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Model;

class Observer
{
    public function __construct(
        \OmegaCommerce\Api\Indexer $indexer
    )
    {
        $this->indexer = $indexer;
        if ($this->isReindexByCronEnabled()) {
            return;
        }
        if (get_option(\OmegaCommerce\Model\Config::NOTICE_FLAG_ASK_REINDEX)){ //don't do sync before full manual sync
            return;
        }

//        add_action('in_admin_footer', array(&$this, 'check'));

        add_action("woocommerce_api_create_product", array(&$this, "run"), 10, 4);
        add_action("woocommerce_api_edit_product", array(&$this, "run"), 10, 4);
        add_action("save_post", array(&$this, "run"), 10, 4);

        add_action("wp_trash_post", array(&$this, "run"), 10, 4);
        add_action("before_delete_post", array(&$this, "run"), 10, 4);

        add_action("after_delete_post", array(&$this, "run"), 10, 4);
        add_action("trashed_post", array(&$this, "run"), 10, 4);

        add_action("created_category", array(&$this, "run"), 10, 4);
        add_action("edited_category", array(&$this, "run"), 10, 4);
        add_action("delete_category", array(&$this, "run"), 10, 4);

        add_action("save_post", array(&$this, "run"), 10, 4);

        add_action("created_product_cat", array(&$this, "run"), 10, 4);
        add_action("edited_product_cat", array(&$this, "run"), 10, 4);
        add_action("delete_product_cat", array(&$this, "run"), 10, 4);

    }

    public function isReindexByCronEnabled()
    {
        return get_option('omega_api_sync_mode') == \OmegaCommerce\Model\Config::REINDEX_BY_CRON;
    }

    public function run() {
        try {
            $this->reindex();
        } catch(\Exception $e){} //don't fail if error
    }

    public function check() {
        //we need to hide errors here, to avoid possible messages in admin panel.
        $currentLevel = error_reporting();
        $current = ini_get('display_errors');
        ini_set('display_errors', 0);
        if (get_option('omega_api_sync_time') < time()) {
            $start = microtime(true);
            $hasChanges = false;
            try {
                $hasChanges = $this->reindex();
            } catch(\Exception $e){} //don't fail if error
            if ($hasChanges) {
                update_option('omega_api_sync_time', time()+1*60);
            } else {
                update_option('omega_api_sync_time', time()+10*60);
            }
            $time_elapsed_secs = microtime(true) - $start;
            echo "<br><br><b>Time taken:".$time_elapsed_secs."</b>";
        }
        //restore default settings
        ini_set('display_errors', $current);
        error_reporting($currentLevel);
    }

    public function reindex() {
        $hasChanges = false;
        foreach ($this->indexer->getEntities() as $entity) {
            $count = $this->indexer->removeEntity($entity, 3);
            if ($count > 0) {
                $hasChanges = true;
            }
            $count = $this->indexer->reindexQueueLength($entity);
            if ($count > 0) {
                $hasChanges = true;
                $this->indexer->reindexEntity($entity, 3);
            }
        }
        return $hasChanges;
    }

}