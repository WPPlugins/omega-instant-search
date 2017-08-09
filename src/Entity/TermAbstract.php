<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Entity;

use OmegaCommerce\Api\Entity\Table;
use OmegaCommerce\Api\Interfaces\EntityInterface;

abstract class TermAbstract implements EntityInterface
{
    /**
     * @return string
     */
    abstract function getTaxonomyType();

    /**
     * {@inheritdoc}
     */
    public function getMainTable() {
        global $wpdb;

        $table = new Table($wpdb->prefix."term_taxonomy");
        $table->addIDField("term_id");
        $table->addField("description");
        $table->setWhere("main.taxonomy = '{$this->getTaxonomyType()}'");
        return $table;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkedTables() {
        global $wpdb;

        $table = new Table($wpdb->prefix."terms");
        $table->addIDField("term_id");
        $table->addField("name");
        $table->addField("slug");
        $table->addLeftJoin($wpdb->prefix."term_taxonomy tt ON main.term_id = tt.term_id");
        $table->setWhere("tt.taxonomy = '{$this->getTaxonomyType()}'");
        return array($table);
    }
}

