<?php

namespace OutOfStockReminder\Grid\Filters;

use OutOfStockReminder\Grid\Definition\Factory\RuleGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

class RuleFilters extends Filters
{
    protected $filterId = RuleGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
