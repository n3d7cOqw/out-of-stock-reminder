<?php

namespace OutOfStockReminder\Controller\Admin;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Context;

class SearchProductController extends FrameworkBundleAdminController
{

    public function search(Request $request): JsonResponse
    {

        $sql = new \DbQuery();
        $sql->select("pl.id_product, pl.name, p.reference")
            ->from("product_lang", "pl")
            ->rightJoin("product", "p", "pl.id_product=p.id_product")
            ->where('pl.name LIKE "%' . pSQL($request->get("search")) . '%" and pl.id_lang ="' . $this->getContext()->language->id . '"');
//        dd($sql);
        $products = \Db::getInstance()->executeS($sql);
//        dd($products);


        $searched_products = array_map(function ($val) {

            $product = new \Product((int)$val['id_product'], false, $this->getContext()->language->id);


            $img = $product->getCover($product->id);
            $img_url = Context::getContext()->link->getImageLink(isset($product->link_rewrite) ? $product->link_rewrite : $product->name, (int)$img['id_image']);
            $val["img"] = $img_url;
            return $val;
        }, $products);
        if (count($products) > 0) {
            return $this->json($searched_products);
        } else {
            $sql = new \DbQuery();
            $sql->select("pl.id_product, pl.name, p.reference")
                ->from("product_lang", "pl")
                ->rightJoin("product", "p", "pl.id_product=p.id_product")
                ->where('pl.id_product = ' . pSQL($request->get("search")) . ' and pl.id_lang ="' . $this->getContext()->language->id . '"');
//        dd($sql);
            $products = \Db::getInstance()->executeS($sql);
//        dd($products);


            $searched_products = array_map(function ($val) {

                $product = new \Product((int)$val['id_product'], false, $this->getContext()->language->id);


                $img = $product->getCover($product->id);
                $img_url = Context::getContext()->link->getImageLink(isset($product->link_rewrite) ? $product->link_rewrite : $product->name, (int)$img['id_image']);
                $val["img"] = $img_url;
                return $val;
            }, $products);
            if (count($products) > 0) {
                return $this->json($searched_products);
            } else {


                $sql = new \DbQuery();
                $sql->select("pl.id_product, pl.name, p.reference")
                    ->from("product_lang", "pl")
                    ->rightJoin("product", "p", "pl.id_product=p.id_product")
                    ->where('p.reference LIKE "%' . pSQL($request->get("search")) . '%" and pl.id_lang ="' . $this->getContext()->language->id . '"');
                $products = \Db::getInstance()->executeS($sql);
                $searched_products = array_map(function ($val) {

                    $product = new \Product((int)$val['id_product'], false, $this->getContext()->language->id);
                    $img = $product->getCover($product->id);
                    $img_url = Context::getContext()->link->getImageLink(isset($product->link_rewrite) ? $product->link_rewrite : $product->name, (int)$img['id_image']);
                    $val["img"] = $img_url;
                    return $val;
                }, $products);


                if (count($searched_products) > 0) {
                    return $this->json($searched_products);
                } else {
                    return $this->json(["response" => $this->trans("result not found", "Modules.OutOfStockReminder.Admin")]);
                }


            }

        }
    }
}