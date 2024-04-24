<?php

namespace OutOfStockReminder\Controller\Admin;

use Category;
use Context;
use Helper;
use HelperTreeCategories;

use OutOfStockReminder\Grid\Definition\Factory\RuleGridDefinitionFactory;
use OutOfStockReminder\Grid\Filters\RuleFilters;
use OutOfStockReminder\Validator\RuleValidator;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteria;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OutOfStockReminder\Entity\Rule;
use OutOfStockReminder\Form\RuleType;

class OutOfStockReminderController extends FrameworkBundleAdminController
{
    /**
     * List quotes
     *
     * @param RuleFilters $filters
     *
     * @return Response
     */
    public function indexAction(RuleFilters $filters)
    {
        $quoteGridFactory = $this->get('out_of_stock_reminder.grid.factory.rules');
        $quoteGrid = $quoteGridFactory->getGrid($filters);

        $gridView = $this->presentGrid($quoteGrid);
        $link = \Context::getContext()->link;
        $url = $link->getAdminLink("OutOfStockReminder", true, ["route" => "out_of_stock/create_rule"]);
        $layoutTitle = $this->trans('Out of stock Reminder', 'Modules.OutOfStockReminder.Admin');
        $layoutHeaderToolbarBtn = $this->getToolbarButtons();
        return $this->render("@Modules/outofstockreminder/views/templates/admin/index.html.twig", compact("url", "gridView", "layoutTitle", "layoutHeaderToolbarBtn"));

    }

    private function getToolbarButtons()
    {
        return [
            'add' => [
                'desc' => $this->trans('Add new rule', 'Modules.OutOfStockReminder.Admin'),
                'icon' => 'add_circle_outline',
                'href' => $this->generateUrl('out_of_stock/create_rule'),
            ],
        ];
    }

    public function searchAction(Request $request)
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('out_of_stock_reminder.grid.definition.factory.rules'),
            $request,
            RuleGridDefinitionFactory::GRID_ID,
            'out_of_stock_rules'
        );
    }

    public function createAction(Request $request,)
    {
        if ($request->query->get("formData") !== null) {
            $form = $this->createForm(RuleType::class, [], ["action" => $this->generateUrl("sent_rule"), "method" => "POST"]);
            $form->get("title")->setData($request->get("formData")["title"]);
            $form->get("threshold")->setData($request->get("formData")["threshold"]);
            $form->get("category_id")->setData($request->get("formData")["category_id"] ?? null);
            $form->get("product")->setData($request->get("formData")["product"] ?? null);
            $form->get("threshold")->setData($request->get("formData")["threshold"]);
            $form->get("status")->setData($request->get("formData")["status"]);
            $form->get("email")->setData($request->get("formData")["email"]);
        } else {
            $form = $this->createForm(RuleType::class, [], ["action" => $this->generateUrl("sent_rule"), "method" => "POST"]);
        }
        $formView = $form->createView();
        return $this->render("@Modules/outofstockreminder/views/templates/admin/create.html.twig", compact("formView"));
    }

    public function storeAction(Request $request)
    {
        if (count($errors = RuleValidator::isSelectedProductOrCategory($request)) !== 0){
            $this->flashErrors($errors);
            return $this->redirectToRoute("out_of_stock/create_rule", ["formData" => $request->request->all()["rule"]]);
        }
        $form = $this->createForm(RuleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (RuleValidator::isValidForm($form) === []) {
                $em = $this->getDoctrine()->getManager();

                $title = trim($form->get('product')->getData());
                $sql = new \DbQuery();
                $sql->select("id_product")
                    ->from("product_lang")
                    ->where('name = "' . pSQL($title) . '" and id_lang ="' . $this->getContext()->language->id . '"')
                    ->orderBy("name");
                $id_product = \Db::getInstance()->executeS($sql);

                if (!isset($id_product[0]["id_product"]) && RuleValidator::isValidTitle($title)) {

                    $this->addFlash("error", $this->trans("The product " . $request->request->get("rule")["product"] . " wasn't found", "Modules.OutOfStockReminder.Admin"));
                    return $this->redirectToRoute("out_of_stock/create_rule");

                }

                $rule = new Rule();
                $rule->setTitle(trim($request->get("rule")["title"]));
                $rule->setProductId($id_product[0]["id_product"] ?? null);
                $rule->setCategoryId($form->get("category_id")->getData());
                $rule->setThreshold($request->get("rule")["threshold"]);
                $rule->setStatus($request->get("rule")["status"]);
                $rule->setEmail(trim($request->get("rule")["email"]));

                if ($form->get("select_all_categories")->getData() === 1) {
                    $rule->setCategoryId(0);
                }
                if ($request->get("rule")["status"] == "1") {


                    if ($rule->getCategoryId() !== null) {
                        $condition = "r.category_id = " . $rule->getCategoryId();
                    } else if ($rule->getProductId() !== null) {
                        $condition = "r.product_id = " . $rule->getProductId();
                    } else {
                        $this->addFlash("error", $this->trans("The product " . $request->request->get("rule")["product"] . " wasn't found", "Modules.OutOfStockReminder.Admin"));
                        return $this->redirectToRoute("out_of_stock/create_rule", ["formData" => $request->request->all()["rule"]]);
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

                $this->addFlash("success", $this->trans("The rule successfully created ", "Modules.OutOfStockReminder.Admin"));

                return $this->redirectToRoute("out_of_stock_rules");

            } else {

                $this->flashErrors(RuleValidator::isValidForm($form));

                if (!RuleValidator::isOneRule($request)) {

                    $this->addFlash("error", $this->trans("Select category or product", "Modules.OutOfStockReminder.Admin"));

                }
                return $this->redirectToRoute("out_of_stock/create_rule", ["formData" => $request->request->all()["rule"]]);
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
            $sql->select("name")->from("product_lang")
                ->where('id_product = "' . $rule->getProductId() . '" and id_lang ="' . $this->getContext()->language->id . '"')->orderBy("name");
            $product = \Db::getInstance()->executeS($sql)[0]["name"];
            $data["product"] = $product;

        }

        if ($rule->getCategoryId() !== null) {

            $data["category_id"] = $rule->getCategoryId();

        }
        $category_id = $rule->getCategoryId();
        $form = $this->createForm(RuleType::class, $data, ["action" => $this->generateUrl("out_of_stock_update", ["id" => $id]), "method" => "POST"]);
        $formView = $form->createView();

        if ($rule->getProductId()) {

            $sql = new \DbQuery();
            $sql->select("pl.id_product, pl.name, p.reference")
                ->from("product_lang", "pl")
                ->rightJoin("product", "p", "pl.id_product=p.id_product")
                ->where('pl.id_product = ' . pSQL($rule->getProductId()) . ' and pl.id_lang ="' . $this->getContext()->language->id . '"');
            $productInfo = \Db::getInstance()->executeS($sql);
            $product = new \Product((int)$productInfo[0]['id_product'], false, $this->getContext()->language->id);
            $img = $product->getCover($product->id);
            $img_url = Context::getContext()->link->getImageLink(isset($product->link_rewrite) ? $product->link_rewrite : $product->name, (int)$img['id_image']);
            $productInfo[0]["img"] = $img_url;
            $productInfo = $productInfo[0];
            return $this->render("@Modules/outofstockreminder/views/templates/admin/edit.html.twig", compact("formView", "category_id", "productInfo"));
        }
        return $this->render("@Modules/outofstockreminder/views/templates/admin/edit.html.twig", compact("formView", "category_id"));
    }

    public function updateAction(int $id, Request $request)
    {
        if (count($errors = RuleValidator::isSelectedProductOrCategory($request)) !== 0){
            $this->flashErrors($errors);
            return $this->redirectToRoute("out_of_stock_edit", ["id" => $id]);
        }

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
        if ($form->isSubmitted() && $form->isValid() && RuleValidator::isValidForm($form) === [] && RuleValidator::isOneRule($request)) {
            $sql = new \DbQuery();
            $sql->select("id_product")
                ->from("product_lang")
                ->where('name = "' . pSQL(trim($form->get("product")->getData())) . '" and id_lang ="' . $this->getContext()->language->id . '"')
                ->orderBy("name");
            $id_product = \Db::getInstance()->executeS($sql);

            if (!isset($id_product[0]["id_product"]) && RuleValidator::isValidTitle(trim($form->get("product")->getData()))) {
                $this->addFlash("error", $this->trans("The product " . $request->request->get("rule")["product"] . " wasn't found", "Modules.OutOfStockReminder.Admin"));
                return $this->redirectToRoute("out_of_stock_edit", ["id" => $id]);

            }

            $rule->setTitle(trim($form->get("title")->getData()));
            $rule->setProductId($id_product[0]["id_product"] ?? null);
            $rule->setCategoryId($form->get("category_id")->getData());
            $rule->setStatus($form->get("status")->getData());
            $rule->setThreshold($form->get("threshold")->getData());
            $rule->setEmail(trim($form->get("email")->getData()));

            if ($form->get("select_all_categories")->getData() === 1) {
                $rule->setCategoryId(0);
            }

            if ($request->get("rule")["status"] == "1") {

                if ($rule->getCategoryId() !== null) {
                    $condition = "r.category_id = " . $rule->getCategoryId();
                } else if ($rule->getProductId() !== null) {
                    $condition = "r.product_id = " . $rule->getProductId();
                } else {
                    $this->addFlash("error", $this->trans("The product " . $request->request->get("rule")["product"] . " wasn't found", "Modules.OutOfStockReminder.Admin"));
                    return $this->redirectToRoute("out_of_stock_edit", ["id" => $id]);
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

            if (!RuleValidator::isOneRule($request)) {
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
        $this->addFlash("success", $this->trans("The rule successfully deleted", "Modules.OutOfStockReminder.Admin"));

        return $this->redirectToRoute("out_of_stock_rules");
    }

}