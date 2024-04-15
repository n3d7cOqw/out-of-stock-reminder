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
        $gridView = $this->presentGrid($quoteGrid);

        $link = \Context::getContext()->link;
        $url = $link->getAdminLink("OutOfStockReminder", true, ["route" => "out_of_stock/create_rule"]);

        return $this->render("@Modules/outofstockreminder/views/templates/admin/index.html.twig", compact("url", "gridView"));

    }


    public function createAction(Request $request)
    {

        $form = $this->createForm(RuleType::class, [], ["action" => $this->generateUrl("sent_rule"), "method" => "POST"]);
        $formView = $form->createView();

        return $this->render("@Modules/outofstockreminder/views/templates/admin/create.html.twig", compact("formView"));
    }

    public function storeAction(Request $request)
    {
        $form = $this->createForm(RuleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (RuleValidator::isValidForm($form) === []) {
                $em = $this->getDoctrine()->getManager();

                $title = trim($form->get('product')->getData());
                $sql = new \DbQuery();
                $sql->select("id_product")
                    ->from("product_lang")
                    ->where('name = "' . $title . '" and id_lang ="' . $this->getContext()->language->id . '"')
                    ->orderBy("name");
                $id_product = \Db::getInstance()->executeS($sql);
                if (!isset($id_product[0]["id_product"]) && RuleValidator::isValidTitle($title)){

                    $this->addFlash("error", $this->trans("The product " . $request->request->get("rule")["product"] . " wasn't found", "Modules.OutOfStockReminder.Admin"));
                    return $this->redirectToRoute("out_of_stock/create_rule");

                }

                $rule = new Rule();
                $rule->setTitle($request->get("rule")["title"]);
                $rule->setProductId($id_product[0]["id_product"] ?? null);
                $rule->setCategoryId($form->get("category_id")->getData()  );
                $rule->setThreshold($request->get("rule")["threshold"]);
                $rule->setStatus($request->get("rule")["status"]);
                $rule->setEmail($request->get("rule")["email"]);

                if ($form->get("select_all_categories")->getData() === 1){
                    $rule->setCategoryId(0);
                }

                if ($request->get("rule")["status"] == "1") {

                    if ($rule->getCategoryId() !== null) {
                        $condition = "r.category_id = " . $rule->getCategoryId();
                    } else if ($rule->getProductId() !== null){
                        $condition = "r.product_id = " . $rule->getProductId();
                    }else{
                        $this->addFlash("error", $this->trans("The product " . $request->request->get("rule")["product"] . " wasn't found", "Modules.OutOfStockReminder.Admin"));
                        return  $this->redirectToRoute("out_of_stock/create_rule");
                    }
                    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                    $query = $qb->select("r")
                        ->from(Rule::class, "r")
                        ->where("r.status = 1")
                        ->andWhere($condition)
                        ->getQuery();
                    $rulesToDisable = $query->getResult();
                    if (count($rulesToDisable) > 0) {
                        foreach ($rulesToDisable as $ruleToDisable) {
                            $ruleToDisable->setStatus(0);
                            $em->persist($ruleToDisable);
                            $em->flush();
                        }

                    }

                }


                $em->persist($rule);
                $em->flush();

                $this->addFlash("success", $this->trans("rule successfully created ", "Modules.OutOfStockReminder.Admin"));

                return $this->redirectToRoute("out_of_stock_rules");

            } else {

                foreach (RuleValidator::isValidForm($form) as $error) {
                    $this->addFlash("error", $this->trans($error, "Modules.OutOfStockReminder.Admin"));

                }

                return $this->redirectToRoute("out_of_stock/create_rule");
            }
        }
        return $this->redirectToRoute("out_of_stock_rules");
    }

    public function editAction(int $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rule = $em->getRepository(Rule::class)->find($id);
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

        $form = $this->createForm(RuleType::class, $data, ["action" => $this->generateUrl("out_of_stock_update", ["id" => $id]), "method" => "POST"]);
        $formView = $form->createView();

        return $this->render("@Modules/outofstockreminder/views/templates/admin/edit.html.twig", compact("formView"));
    }

    public function updateAction(int $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rule = $em->getRepository(Rule::class)->find($id);
        $data = [
            "title" => $request->get("rule")["title"],
            "status" => $request->get("rule")["status"],
            "threshold" => $request->get("rule")["threshold"],
            "email" => $request->get("rule")["email"],
        ];
        $form = $this->createForm(RuleType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && RuleValidator::isValidForm($form) === [] && RuleValidator::isOneRule($request)){
            $sql = new \DbQuery();
            $sql->select("id_product")
                ->from("product_lang")
                ->where('name = "' . trim($form->get("product")->getData()) . '" and id_lang ="' . $this->getContext()->language->id . '"')
                ->orderBy("name");
            $id_product = \Db::getInstance()->executeS($sql);

            if (!isset($id_product[0]["id_product"]) && RuleValidator::isValidTitle(trim($form->get("product")->getData())) ){
                $this->addFlash("error", $this->trans("The product " . $request->request->get("rule")["product"] . " wasn't found", "Modules.OutOfStockReminder.Admin"));
                return $this->redirectToRoute("out_of_stock_edit", ["id" => $id]);

            }

            $rule->setTitle(trim($form->get("title")->getData()));
            $rule->setProductId($id_product[0]["id_product"] ?? null);
            $rule->setCategoryId($form->get("category_id")->getData() );
            $rule->setStatus($form->get("status")->getData());
            $rule->setThreshold($form->get("threshold")->getData());
            $rule->setEmail(trim($form->get("email")->getData()));

            if ($form->get("select_all_categories")->getData() === 1){
                $rule->setCategoryId(0);
            }

            if ($request->get("rule")["status"] == "1") {

                    if ($rule->getCategoryId() !== null) {
                        $condition = "r.category_id = " . $rule->getCategoryId();
                    } else if ($rule->getProductId() !== null){
                        $condition = "r.product_id = " . $rule->getProductId();
                    }else{
                        $this->addFlash("error", $this->trans("The product " . $request->request->get("rule")["product"] . " wasn't found", "Modules.OutOfStockReminder.Admin"));
                         return  $this->redirectToRoute("out_of_stock_edit", ["id" => $id]);
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
                            $ruleToDisable->setStatus(0);
                            $em->persist($ruleToDisable);
                            $em->flush();
                        }

                    }

                }

            $em->persist($rule);
            $em->flush();

            $this->addFlash("success", $this->trans("The rule successfully updated", "Modules.OutOfStockReminder.Admin"));

            return $this->redirectToRoute("out_of_stock_rules");

        } else {

            $this->flashErrors(RuleValidator::isValidForm($form));

            if (!RuleValidator::isOneRule($request)){
                $this->addFlash("error", $this->trans("Select category or product", "Modules.OutOfStockReminder.Admin"));

            }


            return $this->redirectToRoute("out_of_stock_edit", ["id" => $id]);
        }
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