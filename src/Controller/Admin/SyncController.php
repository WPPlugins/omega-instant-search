<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Controller\Admin;

class SyncController
{
    protected $results, $errorMessage, $errorCode, $stepN, $operation, $entityIndex, $total, $currentTotal;

    /**
     * @var \OmegaCommerce\Api\Interfaces\EntityInterface
     */
    protected $nextEntity;

    public function __construct(
        \OmegaCommerce\Api\Config $config,
        \OmegaCommerce\Api\Auth $auth,
        \OmegaCommerce\Api\Indexer $indexer
    )
    {
        $this->config = $config;
        $this->auth = $auth;
        $this->indexer = $indexer;

        add_action('admin_enqueue_scripts', function($hook){
            if ('admin_page_omega_commerce_sync' == $hook) {
                wp_enqueue_script('omega_sync_script',
                    sprintf("%ssrc/view/js/sync.js", WP_OMEGA_COMMERCE_PLUGIN_URL), array(), false, true);
                wp_enqueue_style('omega_sync_css',
                    sprintf("%ssrc/view/css/sync.css", WP_OMEGA_COMMERCE_PLUGIN_URL));
            }
        });

        add_action('wp_ajax_omega_data_sync', array(&$this, 'syncAction'));
    }

    public function step($num, $entityIndex, $action) {
        return $num."_".$entityIndex."_".$action;
    }

    /**
     * AJAX callback handler
     */
    public function syncAction()
    {
        if (isset($_GET['d'])) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
        $nonce = $_REQUEST['step_nonce'];
        $this->stepN = (int)$_REQUEST['step'];
        $this->operation = $_REQUEST['operation'];
        $this->entityIndex = $_REQUEST['entity'];

        if (!wp_verify_nonce($nonce, 'omega-commerce-sync-nonce')) {
            echo '';
            wp_die();
        }
        if(!session_id()) {
            session_start();
        }
        global $_SESSION;

        delete_option(\OmegaCommerce\Model\Config::NOTICE_FLAG_ASK_REINDEX);

        try {
            $this->run();
        } catch(\OmegaCommerce\Api\Exception $e) {
            $this->errorCode = $e->getCode();
            $this->errorMessage = $e->getMessage();
            status_header($this->errorCode);
        }
        ob_start();
        include(sprintf("%ssrc/view/templates/syncResult.php", WP_OMEGA_COMMERCE_PLUGIN_PATH));
        $html = ob_get_contents();
        ob_end_clean();

        echo json_encode(array(
            "html" => $html,
            "next_step" => $this->stepN,
            "next_operation" => $this->operation,
            "next_entity" => $this->entityIndex,
        ));

        wp_die();
    }

    const ACTION_INIT = "init";
    const ACTION_CLEANUP = "cleanup";
    const ACTION_REINDEX = "reindex";
    const ACTION_FINISH = "finish";

    public function run()
    {
        $entities = array();
        foreach($this->indexer->getEntities() as $entity) {
            $entities[$entity->getType()] = $entity;
        }
        if ($this->operation == self::ACTION_INIT) {
            $this->operation = self::ACTION_CLEANUP;
            $this->stepN = 1;
            $this->entityIndex = array_keys($entities)[0];
            $this->currentTotal = -1;
            $this->total = -1;
        }

        $limit = get_option('omega_api_max_sync_number');

        $entity = $entities[$this->entityIndex];

        if ($this->stepN == 1) {
            $_SESSION["omega_total"] = $this->indexer->reindexQueueLength($entity);
        }
        if ($this->operation == self::ACTION_CLEANUP) {
            $count = $this->indexer->removeEntity($entity, $limit);
            if ($count == 0) {
                $this->operation = self::ACTION_REINDEX;
            }
//            $this->stepN++;
        }
        if ($this->operation == self::ACTION_REINDEX) {
            $this->indexer->reindexEntity($entity, $limit);
            $this->total = $_SESSION["omega_total"];
            $this->currentTotal = $this->stepN * $limit;

            //30 * 27 > 800 && 30 * 27 < 800 + 30
            if ($this->currentTotal > $this->total && $this->currentTotal < $this->total + $limit) {
                $this->currentTotal = $this->total;
                $this->stepN++;
                $this->nextEntity = $entity;
                //30 * 28 > 800
            } else if ($this->currentTotal > $this->total) {
                $this->stepN = 1;
                $this->nextEntity = false;
                do {
                    $current = current($entities);
                    if ($this->entityIndex == $current->getType()) {
                        next($entities);
                        $current = current($entities);
                        if ($current) {
                            $this->entityIndex = $current->getType();
                            $this->nextEntity = $current;
                        }
                        break;
                    }
                } while(next($entities));

                if ($this->nextEntity) {
                    $this->operation = self::ACTION_CLEANUP;
                } else {
                    $this->operation = self::ACTION_FINISH;
                }
            } else {
                $this->nextEntity = $entity;
                $this->stepN++;
            }
        }
    }

    /**
     *  Show sync page handler
     */
    public function showAction()
    {
        if (!$this->auth->isAuthorized()) {
            $this->auth->register(get_site_url());
        }

        if (isset($_GET["clear"])) {
            try {
                $this->indexer->clean();
            } catch(\OmegaCommerce\Api\Exception $e) {
                echo "</br>";
                echo $e->getMessage();
                die;
            }
        }
        if (!current_user_can(\OmegaCommerce\Model\Menu::OMEGA_COMMERCE_CAPABILITY)) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        include(sprintf("%ssrc/view/templates/syncPage.php", WP_OMEGA_COMMERCE_PLUGIN_PATH));
    }

    /**
     *  Show sync page handler
     */
    public function showButtonAction()
    {
        if (!current_user_can(\OmegaCommerce\Model\Menu::OMEGA_COMMERCE_CAPABILITY)) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        if (isset($_GET["clear"])) {
            try {
                $this->indexer->clean();
            } catch(\OmegaCommerce\Api\Exception $e) {
                echo "</br>";
                echo $e->getMessage();
                die;
            }
            echo '<br><div class="update-nag" style="border-left: 4px solid #008000;width: 100%;">Indexes are empty. Please run data synchronisation.</div>';
        }
        include(sprintf("%ssrc/view/templates/syncButtonPage.php", WP_OMEGA_COMMERCE_PLUGIN_PATH));
    }
}