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
        $sql->select("DISTINCT pl.id_product, pl.name, p.reference")
            ->from("product_lang", "pl")
            ->leftJoin("product", "p", 'pl.id_product=p.id_product and pl.id_lang ="' . $this->getContext()->language->id . '"')
            ->where('pl.name LIKE "%' . pSQL($request->get("search")) .
                '%" OR p.reference LIKE "%' . pSQL($request->get("search")) . '%" or p.id_product = "' . pSQL($request->get("search")) . '"')
            ->limit(10);
        $products = \Db::getInstance()->executeS($sql);
        $searched_products = array_map(function ($val) {
            $product = new \Product((int)$val['id_product'], false, $this->getContext()->language->id);
            $img = $product->getCover($product->id);
            $img_url = Context::getContext()->link->getImageLink('product', $img["id_image"], "small_default");
            $val["img"] = $img_url;
            return $val;
        }, $products);
        if (count($products) > 0) {
            return $this->json($searched_products);
        } else {
            return $this->json(["response" => $this->trans("result not found", "Modules.OutOfStockReminder.Admin")]);
        }
    }
}