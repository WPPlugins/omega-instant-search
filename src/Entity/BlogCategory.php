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

class BlogCategory extends TermAbstract implements EntityInterface
{
    public function __construct(
        \OmegaCommerce\Entity\Helper\BlogCategory $categoryHelper
    )
    {
        $this->blogCategoryHelper = $categoryHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxonomyType() {
        return "category";
    }


    /**
     * {@inheritdoc}
     */
    public function getType() {
        return "blog_category";
    }

    /**
     * {@inheritdoc}
     */
    public function getHumanName() {
        return "blog categories";
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByIds($ids){
        $categories = get_categories(array(
            'taxonomy' => 'category',
            'hide_empty' => false,
            'include' => $ids,
        ));
        $items = array();
        foreach($categories as $category) {
            $items[] = $this->blogCategoryHelper->getData($category);
        }
        return $items;
    }
}

