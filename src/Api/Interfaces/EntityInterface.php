<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Api\Interfaces;

interface EntityInterface
{
    /**
     * @return \OmegaCommerce\Api\Entity\Table
     */
    public function getMainTable();

    /**
     * @return \OmegaCommerce\Api\Entity\Table[]
     */
    public function getLinkedTables();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getHumanName();

    /**
     * @param  $ids int[]
     * @return array
     */
    public function getDataByIds($ids);
}