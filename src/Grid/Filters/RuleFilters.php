<?php

namespace OutOfStockReminder\Grid\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;

class RuleFilters extends Filters
{

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
