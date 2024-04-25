<?php

use OutOfStockReminder\Entity\Rule;

class OutOfStockReminderEmailModuleFrontController extends ModuleFrontController
{
    public $auth = false;

    public $ajax;


    public function display()
    {

        //products
        $sql = new \DbQuery();
        $sql->select("DISTINCT r.title, r.threshold, r.product_id, r.status, r.email, p.quantity, pl.name")
            ->from("out_of_stock_rules", "r")
            ->leftJoin("stock_available", "p", "p.id_product = r.product_id")
            ->leftJoin("product_lang", "pl", "pl.id_product = r.product_id" )
            ->where("r.status = 1 AND r.product_id IS NOT NULL and p.id_product_attribute = 0");

        $rules = Db::getInstance()->executeS($sql);

        $checkedRules = [];
        foreach ($rules as $rule) {
            $checkedRules[] = $rule["product_id"];
            $emails = explode(" ", $rule["email"]);if ($rule["threshold"] > $rule["quantity"]) {
                Mail::Send(
                    (int)(Configuration::get('PS_LANG_DEFAULT')),
                    'outofstockreminder',
                    $this->trans('Out of Stock', [], "Emails.Subject"),
                    array(
                        '{message}' => $this->trans('The rule ' . $rule["title"]
                                . ' has been exceeded for product' . $rule["name"]. ' with id - '. $rule["product_id"] . '. Please change quantity of stock goods. Threshold is ' . $rule["threshold"] . ", product quantity is " . $rule["quantity"] , [],
                                "Emails.Body") ,
                    ),
                    $emails,
                    NULL, NULL, NULL, NULL, NULL, _PS_MODULE_DIR_ . 'outofstockreminder/mails'
                );

            }

        }

        //categories
        $sql = new \DbQuery();
        $sql->select("DISTINCT r.title, r.threshold, r.category_id, r.product_id, r.status, r.email")
            ->from("out_of_stock_rules", "r")
            ->where("r.status = 1 AND r.category_id IS NOT NULL ");
        $rules = Db::getInstance()->executeS($sql);


        foreach ($rules as $rule) {
            if ($rule["category_id"] !== "0") {
                $sql = new \DbQuery();
                $sql->select("id_product")
                    ->from("product")
                    ->where("id_category_default = " . $rule["category_id"]);
                $products = Db::getInstance()->executeS($sql);
                foreach ($products as $product) {
                    if (!in_array($product["id_product"], $checkedRules)) {
                        $checkedRules[] = $product["id_product"];
                        $sql = new \DbQuery();
                        $sql->select("DISTINCT sa.quantity, pl.name, pl.id_product")
                            ->from("stock_available", "sa")
                            ->leftJoin("product_lang", "pl", "pl.id_product = sa.id_product" )
                            ->where("sa.id_product = " . $product["id_product"] . " AND id_product_attribute = 0");

                        $productInfo = Db::getInstance()->executeS($sql);
                        $emails = explode(" ", $rule["email"]);
                        if ($rule["threshold"] > $productInfo[0]["quantity"]) {Mail::Send(
                                (int)(Configuration::get('PS_LANG_DEFAULT')),
                                'outofstockreminder',
                                $this->trans('Out of Stock', [], "Emails.Subject"),
                                array(
                                    '{email}' => Configuration::get('PS_SHOP_EMAIL'),
                                    '{message}' => $this->trans('The rule ' . $rule["title"]
                                        . ' has been exceeded for product' . $productInfo[0]["name"]. ' with id - '. $productInfo[0]["id_product"] . '. Please change quantity of stock goods. Threshold is ' . $rule["threshold"] . ", product quantity is " . $productInfo[0]["quantity"] , [],
                                        "Emails.Body") ,
                                ),
                                $emails,
                                NULL, NULL, NULL, NULL, NULL, _PS_MODULE_DIR_ . 'outofstockreminder/mails'

                            );
                        }
                    }
                }
            }
        }


        //default rule
        $sql = new \DbQuery();
        $sql->select("*")
            ->from("out_of_stock_rules",)
            ->where("status = 1 AND category_id = 0 ");
        $defaultRule = Db::getInstance()->executeS($sql);

        if (count($defaultRule) > 0) {
            $sql = new \DbQuery();
            $sql->select("DISTINCT p.name, s.quantity, p.id_product")
                ->from("stock_available", "s")
                ->innerJoin("product_lang", "p", "p.id_product = s.id_product")
                ->where("s.quantity < " . $defaultRule[0]["threshold"] . " and s.id_product_attribute = 0")
                ->where("id_lang = " . $this->context->language->id);
            $products = Db::getInstance()->executeS($sql);
            foreach ($products as $product) {
                if (!in_array($product["id_product"], $checkedRules)) {
                    if ($product["quantity"] < $defaultRule[0]["threshold"]) {
                        $emails = explode(" ", $defaultRule[0]["email"]);Mail::Send(
                            (int)(Configuration::get('PS_LANG_DEFAULT')),
                            'outofstockreminder',
                            $this->trans('Out of Stock', [], "Emails.Subject"),
                            array(
                                '{email}' => Configuration::get('PS_SHOP_EMAIL'),
                                '{message}' => $this->trans('The rule ' . $defaultRule[0]["title"]
                                    . ' has been exceeded for product' . $product["name"]. ' with id - '. $product["id_product"] . '. Please change quantity of stock goods. Threshold is ' . $defaultRule[0]["threshold"] . ", product quantity is " . $product["quantity"] , [],
                                    "Emails.Body") ,
                            ),
                            $emails,
                            NULL, NULL, NULL, NULL, NULL, _PS_MODULE_DIR_ . 'outofstockreminder/mails'
                        );
                    }
                }
            }
        }
        exit();
    }
}