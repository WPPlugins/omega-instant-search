<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Controller\Admin;

class DebugController
{
    public function __construct(
        \OmegaCommerce\Api\Config $config,
        \OmegaCommerce\Api\Auth $auth,
        \OmegaCommerce\Api\Indexer $indexer
    )
    {
        $this->config = $config;
        $this->auth = $auth;
        $this->indexer = $indexer;
    }

    /**
     * {@inheritdoc}
     */
    public function showAction()
    {
        if (!current_user_can(\OmegaCommerce\Model\Menu::OMEGA_COMMERCE_CAPABILITY)) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $links = array(
            "show entity" => "?page=omega_commerce_debug&action=show_entity&entity_type=product&id=0",
            "reindex entity" => "?page=omega_commerce_debug&action=reindex_entity&entity_type=product&id=0",
            "cleanStatusTable" => "?page=omega_commerce_debug&action=cleanStatusTable",
        );


        $action = "";
        if (isset($_GET["action"])) {
            $action = $_GET["action"];
        }
        switch ($action) {
            case "show_entity":
                $this->showEntity($_GET["entity_type"], $_GET["id"]);
                break;
            case "reindex_entity":
                $this->reindexEntity($_GET["entity_type"], $_GET["id"]);
                break;
            case "cleanStatusTable":
                $this->cleanStatusTable();
                break;
            default:
                foreach($links as $name=>$link) {
                    echo "<a href='$link'>$name</a><br>";
                }
        }
    }

    public function showEntity($entityType, $id) {
        $entity = false;
        foreach($this->indexer->getEntities() as $e) {
            if ($e->getType() == $entityType) {
                $entity = $e;
            }
        }
        if (!$entity) {
            die("cant find entity $entityType");
        }
        if ($id == 0) {
            die("id is empty");
        }
        echo "<pre>";
        print_r($entity->getDataByIds(array($id)));
        echo "</pre>";
    }

    public function reindexEntity($entityType, $id) {
        $entity = false;
        foreach($this->indexer->getEntities() as $e) {
            if ($e->getType() == $entityType) {
                $entity = $e;
            }
        }
        if (!$entity) {
            die("cant find entity $entityType");
        }
        if ($id == 0) {
            die("id is empty");
        }
        $this->indexer->reindexEntityByIDs($entity, array($id));
        echo "DONE";
    }


    public function cleanStatusTable() {
        $this->indexer->cleanOnlyStatusTable();
        echo "DONE";
    }
}