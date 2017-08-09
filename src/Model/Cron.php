<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Model;

class Cron
{

    public function __construct(
        \OmegaCommerce\Api\Indexer $indexer
    )
    {
        $this->indexer = $indexer;
        if (!$this->isReindexByCronEnabled()) {
            return;
        }

        add_filter('cron_schedules', array(&$this, 'add_custom_schedule'));
        add_action('omega_search_reindex', array(&$this, 'run'));

        if (!wp_next_scheduled('omega_search_reindex')) {
            wp_schedule_event(time(), 'every_5_minutes', 'omega_search_reindex');
        }
    }


    public function isReindexByCronEnabled()
    {
        return get_option('omega_api_sync_mode') == \OmegaCommerce\Model\Config::REINDEX_BY_CRON;
    }


    public function add_custom_schedule($schedules)
    {

        $schedules['every_5_minutes'] = array(
            'interval' => 60 * 5,
            'display' => __('Every 5 Minutes', 'textdomain')
        );

        return $schedules;
    }

    public function run()
    {
        $limit = get_option('omega_api_max_sync_number');
        if ($limit < 1) {
            $limit = 200;
        }
        foreach ($this->indexer->getEntities() as $entity) {
            $this->indexer->removeEntity($entity, $limit);
            $this->indexer->reindexEntity($entity, $limit);
        }
    }
}