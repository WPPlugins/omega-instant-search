<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Api\Entity;


class Table
{
    protected $name;
    protected $where;
    protected $IDfield;
    protected $fields = array();
    protected $leftJoins = array();

    /**
     * @param string $name
     */
    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function addIDField($field) {
        $this->IDfield = $field;
        return $this;
    }


    /**
     * @param string $field
     * @return $this
     */
    public function addField($field) {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getIDField() {
        return $this->IDfield;
    }

    /**
     * @param string $where
     * @return $this
     */
    public function setWhere($where) {
        $this->where = $where;
        return $this;
    }

    /**
     * @return string
     */
    public function getWhere() {
        return $this->where;
    }

    /**
     * @param string $join
     * @return $this
     */
    public function addLeftJoin($join) {
        $this->leftJoins[] = "LEFT JOIN ".$join;
        return $this;
    }

    /**
     * @return array
     */
    public function getLeftJoins() {
        return $this->leftJoins;
    }
}