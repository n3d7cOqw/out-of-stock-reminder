<?php

use PrestaShop\PrestaShop\Core\MailTemplate\FolderThemeScanner;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\Layout;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutVariablesBuilderInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCatalogInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCollectionInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeInterface;


if (!defined("_PS_VERSION_")) {
    exit();
}

class OutOfStockReminder extends Module
{
    public function __construct()
    {
        $this->name = "outofstockreminder";
        $this->tab = "administration";
        $this->version = "1.0";
        $this->author = "Bohdan Kovhan";
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            "min" => "1.8",
            "max" => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans("Products out of stock reminder for administrator", [], "Modules.OutOfStockReminder.Admin");
        $this->description = $this->trans("Reminding if the goods out of stock", [], "Modules.OutOfStockReminder.Admin");
        $this->confirmUninstall = $this->trans("Are you sure that you want to delete this module?", [], "Modules.OutOfStockReminder.Admin");
    }

    public function install()
    {
        return $this->createTable()
            && $this->installTab()
            && parent::install();
    }

    public function uninstall()
    {
        return $this->deleteTable()
            && $this->uninstallTab()
            && parent::uninstall();
    }

    public function createTable()
    {
        $sqlCreate = "CREATE TABLE `" . _DB_PREFIX_ . "out_of_stock_rules` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT, 
        `title` varchar(255) DEFAULT NULL, 
        `product_id` varchar(255) DEFAULT NULL, 
        `category_id` int(11) DEFAULT NULL, 
        `threshold` int(255) DEFAULT NULL, 
        `email` TEXT DEFAULT NULL,
        `status` int(2) DEFAULT NULL, 
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        return Db::getInstance()->execute($sqlCreate);
    }

    public function deleteTable()
    {
        $sql = "DROP TABLE " . _DB_PREFIX_ . "out_of_stock_rules";
        return Db::getInstance()->execute($sql);
    }

    private function installTab()
    {
        $tabId = (int)Tab::getIdFromClassName('OutOfStockReminderController');
        if (!$tabId) {
            $tabId = null;
        }

        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = 'OutOfStockReminderController';
        $tab->route_name = 'out_of_stock_rules';
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->trans('Rules of out stock notification', array(), 'Modules.OutOfStockReminder.Admin');
        }
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminCatalog');
        $tab->module = $this->name;

        return $tab->save();
    }

    private function uninstallTab()
    {
        $tabId = (int)Tab::getIdFromClassName('OutOfStockReminderController');
        if (!$tabId) {
            return true;
        }

        $tab = new Tab($tabId);

        return $tab->delete();
    }

    public function getContent()
    {
        $route = $this->get('router')->generate('out_of_stock_configuration');
        Tools::redirectAdmin($route);
    }
}