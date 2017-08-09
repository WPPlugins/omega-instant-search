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

class Attribute implements EntityInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMainTable() {
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkedTables() {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getType() {
        return "attribute";
    }

    /**
     * {@inheritdoc}
     */
    public function getHumanName() {
        return "attributes";
    }


    /**
     * {@inheritdoc}
     */
    public function getDataByIds($ids){
        $items = array();
        $blog_id = get_current_blog_id();
        foreach (wc_get_attribute_taxonomy_names() as $code) {
                $taxonomy = get_taxonomy($code);
                $label = $taxonomy->label;
                $data = array(
                    'id' => crc32($code),
                    'blog_id' => $blog_id,
                    'name' => $label,
                    'code' => $code,
                    'is_searchable' => true,
                );
            $items[] = $data;
        }
        return $items;
    }

}

