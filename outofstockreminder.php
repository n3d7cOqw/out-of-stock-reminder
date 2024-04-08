<?php

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

        $this->displayName = $this->trans("OutOfStockReminder", [], "Modules.OutOfStockReminder.Admin");
        $this->description = $this->trans("Reminding if the goods ", [], "Modules.OutOfStockReminder.Admin");
        $this->confirmUninstall = $this->trans("Are you sure that you want to delete this module?", [], "Modules.OutOfStockReminder.Admin");
    }

    public function install()
    {
        return $this->createTable()
            && $this->installTab()
            && parent::install()
            && $this->registerHook("actionProductUpdate");
    }

    public function uninstall()
    {
        return $this->deleteTable()
            && $this->uninstallTab()
            && parent::uninstall()
            && $this->unregisterHook("actionProductUpdate");
    }

    public function createTable()
    {
        $sqlCreate = "CREATE TABLE `" . _DB_PREFIX_ . "out_of_stock_rules` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT, 
        `title` varchar(255) DEFAULT NULL, 
        `product_id` varchar(255) DEFAULT NULL, 
        `category_id` int(11) DEFAULT NULL, 
        `threshold` varchar(255) DEFAULT NULL, 
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
            $tab->name[$lang['id_lang']] = $this->trans('Rules of out stock notification', array(), 'Modules.out_of_stock_rules.Admin');
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

    public function hookActionProductUpdate($params)
    {
//        $id_product = $params["id_product"];
//        $quantity = $params["product"]->quantity;
//        $id_category_default = $params["product"]->id_category_default;
//        $sql = new DbQuery();
//        $sql->select("title, threshold, status, email")->from("out_of_stock_rules")->where('product_id = ' . $id_product)->orderBy("id");
//        $rules = Db::getInstance()->executeS($sql);
//        if (count($rules) > 0) {
//            $rule = null;
//
//            foreach ($rules as $a_rule) {
//                if ($a_rule["status"] == "1") {
//                    $rule = $a_rule;
//                    break;
//                }
//
//            }
//
//            if ($quantity > $rule["threshold"]) {
//                $mail = Mail::Send(
//                    (int)(Configuration::get('PS_LANG_DEFAULT')), // defaut language id
//                    'contact', // email template file to be use
//                    'Out of Stock', // email subject
//                    array(
//                        '{email}' => Configuration::get('PS_SHOP_EMAIL'),
//                        '{message}' => 'The rule ' . $rule["title"]. ' has been exceeded. Selected quantity of goods is higher that limit. Please change quantity of stock goods. Threshold is ' . $rule["threshold"] // email content
//                    ),
//                    $rule["email"],
//                    null,
//                    Configuration::get("PS_SHOP_EMAIL")
//
//                );
//            }
//        }else{
//            $sql = new DbQuery();
//            $sql->select("title, threshold, status, email")->from("out_of_stock_rules")->where('category_id = ' . $id_category_default)->orderBy("id");
//            $rules = Db::getInstance()->executeS($sql);
//
//            if (count($rules) > 0) {
//                $rule = null;
//
//                foreach ($rules as $a_rule) {
//                    if ($a_rule["status"] == "1") {
//                        $rule = $a_rule;
//                        break;
//                    }
//
//                }
//
//                if ($quantity > $rule["threshold"]) {
//                    $mail = Mail::Send(
//                        (int)(Configuration::get('PS_LANG_DEFAULT')), // defaut language id
//                        'contact', // email template file to be use
//                        'Out of Stock', // email subject
//                        array(
//                            '{email}' => Configuration::get('PS_SHOP_EMAIL'),
//                            '{message}' => 'The rule ' . $rule["title"]. ' has been exceeded. Selected quantity of goods is higher that limit. Please change quantity of stock goods. Threshold is ' . $rule["threshold"] // email content
//                        ),
//                        $rule["email"],
//                        null,
//                        Configuration::get("PS_SHOP_EMAIL")
//
//                    );
//                }
//            }
//
//        } // реалізовував щоб при оновленні товару також у разі порушення правила відправлявся мейл
    }
}