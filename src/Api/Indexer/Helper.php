<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Api\Indexer;

class Helper
{
    /**
     * @param array $data
     * @param int $depth
     * @return array
     */
    public function prepareDataForApi($data) {
        $r = array();
        foreach ($data as $k => $v) {
            if (!is_object($v) && !is_array($v)) {
                $r[$k] = (string)$v;
            }
            if (is_array($v)) {
                $r[$k] = $this->prepareDataForApi($v);
                continue;
            }
        }
        return $r;
    }
}