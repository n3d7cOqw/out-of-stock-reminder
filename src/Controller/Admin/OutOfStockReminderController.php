<?php

namespace OutOfStockReminder\Controller\Admin;

use Category;
use Context;
use Helper;
use HelperTreeCategories;

use OutOfStockReminder\Grid\Filters\RuleFilters;
use OutOfStockReminder\Validator\RuleValidator;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteria;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OutOfStockReminder\Entity\Rule;
use OutOfStockReminder\Form\RuleType;

class OutOfStockReminderController extends FrameworkBundleAdminController
{
    public function indexAction(RuleFilters $filters)
    {
        $quoteGridFactory = $this->get('out_of_stock_reminder.grid.factory.rules');
        $emptySearchCriteria = new SearchCriteria();
        $quoteGrid = $quoteGridFactory->getGrid($emptySearchCriteria);
        foreach ($quoteGrid->getData()->getRecords() as $record) {
            $record["status"] = "hjd";

        }


        $gridView = $this->presentGrid($quoteGrid);

        $link = \Context::getContext()->link;
        $url = $link->getAdminLink("OutOfStockReminder", true, ["route" => "out_of_stock/create_rule"]);

        return $this->render("@Modules/outofstockreminder/views/templates/admin/index.html.twig", compact("url", "gridView"));

    }


    public function createAction()
    {
        $formView = $this->createForm(RuleType::class)->createView();


        return $this->render("@Modules/outofstockreminder/views/templates/admin/create.html.twig", compact("formView"));
    }

    /**
     *
     *
     * @param Request $request
     *
     * @return Response
     */
    public function storeAction(Request $request)
    {
        $form = $this->createForm(RuleType::class);
        $form->handleRequest($request);
        if ($request->request->has("category_id")) {

            $request->request->set("category_id", 0);

        }
        if ($form->isSubmitted() && $form->isValid() && RuleValidator::isOneRule($request) && RuleValidator::isValidForm($form, $request)) {

            $em = $this->getDoctrine();
            $rule = new Rule();
            $rule->setTitle(trim($form->get('title')->getData()));
            $rule->setThreshold(trim($form->get('threshold')->getData()));
            $rule->setStatus($request->request->get("status"));
            $rule->setEmail(trim($form->get('email')->getData()));

            if (RuleValidator::issetProduct($request)) {
                $title = trim($form->get('product')->getData());
                $sql = new \DbQuery();
                $sql->select("id_product")->from("product_lang")->where('name = "' . $title . '" and id_lang ="' . $this->getContext()->language->id . '"')->orderBy("name");
                $id_product = \Db::getInstance()->executeS($sql);
                if (count($id_product) > 0) {


                    // check if isset any active rules for product
                    if ($request->request->get("status") === "1") {
                        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                        $query = $qb->select("r")
                            ->from(Rule::class, "r")
                            ->where("r.status = 1")
                            ->Andwhere("r.product_id = " . $id_product[0]["id_product"])
                            ->getQuery();
                        $productRules = $query->getResult();
                        if (count($productRules) > 0) {

                            foreach ($productRules as $productRule) {
                                $productRule->setStatus(0);
                                $em->getManager()->persist($productRule);
                                $em->getManager()->flush();
                            }

                        }
                    }

                    $rule->setProductId($id_product[0]["id_product"]);

                } else {
                    // product not found by title
                    $this->addFlash("error", $this->trans("The product " . $request->request->get("rule")["product"] . " wasn't found", "Modules.OutOfStockReminder.Admin"));
                    return $this->redirectToRoute("out_of_stock/create_rule");

                }

            } else {
                // set category id
                $rule->setCategoryId($form->get('category_id')->getData() ?? $request->request->get("category_id"));

                // check if isset any active rules for category

                if ($request->request->get("status") === "1") {
                    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                    $query = $qb->select("r")
                        ->from(Rule::class, "r")
                        ->where("r.status = 1")
                        ->andWhere("r.category_id = " . $rule->getCategoryId())
                        ->getQuery();
                    $categoryRules = $query->getResult();
                    if (count($categoryRules) > 0) {
                        foreach ($categoryRules as $catRule) {
                            $catRule->setStatus(0);
                            $em->getManager()->persist($catRule);
                            $em->getManager()->flush();
                        }

                    }
                }
            }

            $em->getManager()->persist($rule);
            $em->getManager()->flush();

            $this->addFlash("success", $this->trans("The form has sent", "Modules.OutOfStockReminder.Admin"));
            return $this->redirectToRoute("out_of_stock_rules");

        } else {

            $this->addFlash("error", $this->trans("The form was not validated", "Modules.OutOfStockReminder.Admin"));

            return $this->redirectToRoute("out_of_stock/create_rule");

        }


//        return $this->render("@Modules/outofstockreminder/views/templates/admin/create.html.twig", ["formView" => $form->createView()]);

    }

    public function editAction(int $id)
    {
        $em = $this->getDoctrine()->getManager();
        $rule = $em->getRepository(Rule::class)->find($id);
        $status = $rule->getStatus();
        $data = [
            "title" => $rule->getTitle(),
            "status" => $rule->getStatus(),
            "threshold" => $rule->getThreshold(),
            "email" => $rule->getEmail(),

        ];

        if ($rule->getProductId() !== null) {
            $sql = new \DbQuery();
            $sql->select("name")->from("product_lang")->where('id_product = "' . $rule->getProductId() . '" and id_lang ="' . $this->getContext()->language->id . '"')->orderBy("name");
            $product = \Db::getInstance()->executeS($sql)[0]["name"];
            $data["product"] = $product;

        }

        if ($rule->getCategoryId() !== null) {

            $data["category_id"] = $rule->getCategoryId();

        }

        $form = $this->createForm(RuleType::class, $data);
        $formView = $form->createView();
        if (isset($data["category_id"])){
            $category_id = $data["category_id"];
        }else{
            $category_id = null;
        }

        return $this->render("@Modules/outofstockreminder/views/templates/admin/edit.html.twig", compact("formView", "id", "status", "category_id"));
    }

    public function updateAction(int $id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $rule = $em->getRepository(Rule::class)->find($id);


        if ($request->get("rule")["product"] != null) {

            $sql = new \DbQuery();
            $sql->select("id_product")->from("product_lang")->where('name = "' . $request->get("rule")["product"] . '" and id_lang ="' . $this->getContext()->language->id . '"')->orderBy("name");
            $id_product = \Db::getInstance()->executeS($sql);
        }

        if (isset($id_product[0]["id_product"])) {


            $data = [
                "title" => $request->get("rule")["title"],
                "status" => $request->get("status"),
                "threshold" => $request->get("rule")["threshold"],
                "email" => $request->get("rule")["email"],
                "category_id" => $request->get("rule")["category_id"] ?? null,
                "product" => $id_product[0]["id_product"] ?? null,
            ];
            if ($request->request->has("category_id")) {
                $data["category_id"] = 0;
            }
            $form = $this->createForm(RuleType::class, $data);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid() && RuleValidator::isValidForm($form, $request) && RuleValidator::isOneRule($request)) {

                $em = $this->getDoctrine()->getManager();
                $rule->setTitle($data["title"]);
                $rule->setStatus($data["status"]);
                $rule->setThreshold($data["threshold"]);
                $rule->setEmail($data["email"]);
                $rule->setCategoryId($data["category_id"]);
                $rule->setProductId($data["product"]);


                // check if isset any active rules for category or product
                if ($request->get("status") === "1") {

                    if ($rule->getCategoryId() !== null) {
                        $condition = "r.category_id = " . $rule->getCategoryId();
                    } else {
                        $condition = "r.product_id = " . $rule->getProductId()[0];
                    }
                    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                    $query = $qb->select("r")
                        ->from(Rule::class, "r")
                        ->where("r.status = 1")
                        ->andWhere($condition)
                        ->andWhere("r.id != " . $rule->getId())
                        ->getQuery();
                    $rulesToDisable = $query->getResult();
                    if (count($rulesToDisable) > 0) {
                        foreach ($rulesToDisable as $ruleToDisable) {
                            dump($ruleToDisable);
                            $ruleToDisable->setStatus(0);
                            $em->persist($ruleToDisable);
                            $em->flush();
                        }

                    }

                }


                $em->flush();
                $this->addFlash("success", $this->trans("The rule successfully updated", "Modules.OutOfStockReminder.Admin"));
            }
            else{

                $this->addFlash("error", $this->trans("The rule form wasn't validated", "Modules.OutOfStockReminder.Admin"));


            }
        }
        else{

            $this->addFlash("error", $this->trans("The product " . $request->request->get("rule")["product"] . " wasn't found", "Modules.OutOfStockReminder.Admin"));


        }

        return $this->redirectToRoute("out_of_stock_rules");
    }

    public function deleteAction(int $id)
    {
        $em = $this->getDoctrine()->getManager();
        $rule = $em->getRepository(Rule::class)->find($id);
        $em->remove($rule);
        $em->flush();
        $this->addFlash("success", $this->trans("The rule successfully Deleted", "Modules.OutOfStockReminder.Admin"));

        return $this->redirectToRoute("out_of_stock_rules");
    }

}