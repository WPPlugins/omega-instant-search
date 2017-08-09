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

class Category extends TermAbstract implements EntityInterface
{
    public function __construct(
        \OmegaCommerce\Entity\Helper\Category $categoryHelper
    )
    {
        $this->categoryHelper = $categoryHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxonomyType() {
        return "product_cat";
    }

    /**
     * {@inheritdoc}
     */
    public function getType() {
        return "category";
    }

    /**
     * {@inheritdoc}
     */
    public function getHumanName() {
        return "categories";
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByIds($ids){
        $categories = get_categories(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'include' => $ids,
        ));
        $items = array();
        foreach($categories as $category) {
            $items[] = $this->categoryHelper->getData($category);
        }
        return $items;
    }
}

