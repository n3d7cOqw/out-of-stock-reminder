<?php

use Language;

class Ps_Linklist extends Module
{
    public function __construct() {
        $tabNames = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tabNames[$lang['locale']] = $this->trans('Link List', array(), 'Modules.Linklist.Admin', $lang['locale']);
        }
        $this->tabs = [
            [
                'route_name' => 'admin_link_block_list',
                'class_name' => 'AdminLinkWidget',
                'visible' => true,
                'name' => $tabNames,
                'parent_class_name' => 'AdminCatalog',
                'wording' => 'Link List',
                'wording_domain' => 'Modules.Linklist.Admin'
            ],
        ];
    }
}