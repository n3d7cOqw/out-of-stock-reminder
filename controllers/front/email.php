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
        $sql->select("r.title, r.threshold, r.category_id, r.product_id, r.status, r.email, p.quantity")
            ->from("out_of_stock_rules", "r")
            ->where("r.status = 1 AND r.product_id IS NOT NULL ")
            ->innerJoin("stock_available", "p", "p.id_product = r.product_id");

        $rules = Db::getInstance()->executeS($sql);
        $checkedRules = []; // тут айді товарів які перевірено

        foreach ($rules as $rule) {

            $checkedRules[] = $rule["product_id"];

            $emails = explode(" ", $rule["email"]);

            if ($rule["threshold"] < $rule["quantity"]) {

                foreach ($emails as $email) {

                    Mail::Send(
                        (int)(Configuration::get('PS_LANG_DEFAULT')),
                        'contact',
                        $this->trans('Out of Stock', [], "Emails.Subject"),
                        array(
                            '{email}' => Configuration::get('PS_SHOP_EMAIL'),
                            '{message}' => $this->trans('The rule ' . $rule["title"] . ' has been exceeded. Selected quantity of goods is higher that limit. Please change quantity of stock goods. Threshold is ', [], "Emails.Body") . $rule["threshold"],
                            "{order_name}" => "",
                            "{attached_file}" => ""
                        ),
                        $email,
                        null,
                        Configuration::get("PS_SHOP_EMAIL"),
                    );
                }
            }

        }

        //categories

        $sql = new \DbQuery();
        $sql->select("r.title, r.threshold, r.category_id, r.product_id, r.status, r.email")
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
                        $sql->select("quantity")
                            ->from("stock_available")
                            ->where("id_product = " . $product["id_product"]);
                        $quantity = Db::getInstance()->executeS($sql);
                        $emails = explode(" ", $rule["email"]);

                        if ($rule["threshold"] < $quantity[0]["quantity"]) {
                            foreach ($emails as $email) {
                                Mail::Send(
                                    (int)(Configuration::get('PS_LANG_DEFAULT')),
                                    'contact',
                                    $this->trans('Out of Stock', [], "Emails.Subject"),
                                    array(
                                        '{email}' => Configuration::get('PS_SHOP_EMAIL'),
                                        '{message}' => $this->trans('The rule ' . $rule["title"]
                                                . ' has been exceeded. Selected quantity of goods is higher that limit. Please change quantity of stock goods. Threshold is ', [],
                                                "Emails.Body") . $rule["threshold"],
                                        "{order_name}" => "",
                                        "{attached_file}" => ""
                                    ),
                                    $email,
                                    NULL, //receiver name
                                    NULL, //from email address
                                    NULL  //from name

                                );
                            }
                        }
                    }
                }
            }
        }
        //default rule

//        $qb = $entityManager->createQueryBuilder();
//        $query = $qb->select("r")
//            ->from(Rule::class, "r")
//            ->where("r.status = 1")
//            ->Andwhere("r.category_id = 0")
//            ->getQuery();
//        $defaultRule = $query->getResult();

        $sql = new \DbQuery();
        $sql->select("*")
            ->from("out_of_stock_rules", )
            ->where("status = 1 AND category_id = 0 ");

        $defaultRule = Db::getInstance()->executeS($sql);

        if (count($defaultRule) > 0) {
            $sql = new \DbQuery();
            $sql->select("p.name, s.quantity, p.id_product")
                ->from("stock_available", "s")
                ->where("s.quantity > " . $defaultRule[0]["threshold"])
                ->where("id_lang = " . $this->context->language->id)
                ->innerJoin("product_lang", "p", "p.id_product = s.id_product");
            $products = Db::getInstance()->executeS($sql);
            foreach ($products as $product) {

                if (!in_array($product["id_product"], $checkedRules)) {
                    if ($product["quantity"] > $defaultRule[0]["threshold"]) {

                        $emails = explode(" ", $defaultRule[0]["email"]);
                        foreach ($emails as $email) {
                            $email = trim($email); // Удаляем лишние пробелы
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                Mail::Send(
                                    (int)(Configuration::get('PS_LANG_DEFAULT')),
                                    'contact',
                                    $this->trans('Out of Stock', [], "Emails.Subject"),
                                    array(
                                        '{email}' => Configuration::get('PS_SHOP_EMAIL'),
                                        '{message}' => $this->trans('The rule ' . $defaultRule[0]["title"] . ' has been exceeded. Selected quantity of goods is higher than the limit. Please change the quantity of stocked goods. Threshold is ', [], "Emails.Body") . $defaultRule[0]["threshold"],
                                        '{order_name}' => '',
                                        '{attached_file}' => ''
                                    ),
                                    $email,
                                    null,
                                    Configuration::get("PS_SHOP_EMAIL")
                                );
                            }
                        }


                    }
                }
            }
        }
        exit();
    }


}