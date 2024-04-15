<?php

use Language;

class Ps_Linklist extends Module
{
    public function __construct() {
        $tabNames = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tabNames[$lang['locale']] = $this->trans('Link List', array(), 'Modules.OutOfStockReminder.Admin', $lang['locale']);
        }
        $this->tabs = [
            [
                'route_name' => 'out_of_stock_rules',
                'class_name' => 'OutOfStockReminderController',
                'visible' => true,
                'name' => $tabNames,
                'parent_class_name' => 'AdminCatalog',
                'wording' => 'Link List',
                'wording_domain' => 'Modules.OutOfStockReminder.Admin'
            ],
        ];
//        $this->tabs = [
//            [
//                'route_name' => 'out_of_stock_rules',
//                'class_name' => 'OutOfStockReminderController',
//                'visible' => true,
//                'name' => $tabNames,
//                'parent_class_name' => 'AdminCatalog',
//                'wording' => 'Link List',
//                'wording_domain' => 'Modules.OutOfStockReminder.Admin'
//            ],
//        ];
    }
}