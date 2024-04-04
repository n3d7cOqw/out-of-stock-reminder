<?php

namespace OutOfStockReminder\Controller\Admin;

use Helper;
use HelperTreeCategories;

use OutOfStockReminder\Grid\Filters\RuleFilters;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteria;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OutOfStockReminder\Entity\Rule;
use OutOfStockReminder\Form\RuleType;

class OutOfStockReminderController extends FrameworkBundleAdminController
{
    public function indexAction(RuleFilters $filters)
    {
//        $productsGridDefinitionFactory = $this->get('out_of_stock_reminder.grid.definition.factory.rules');
//        $productsGridDefinition = $productsGridDefinitionFactory->getDefinition();
//
//        $emptySearchCriteria = new SearchCriteria();
////        $emptySortingSearchCriteria = new SearchCriteria(
////            [],
////            null,
////            null,
////            2,
////            10
////        );
//
//        $productGridDataFactory = $this->get('out_of_stock_reminder.grid.data_provider.rules');
//        $productGridData = $productGridDataFactory->getData($emptySearchCriteria); // якось працювало
        $quoteGridFactory = $this->get('out_of_stock_reminder.grid.factory.rules');
        $emptySearchCriteria = new SearchCriteria();
        $quoteGrid = $quoteGridFactory->getGrid($emptySearchCriteria);
        $gridView = $this->presentGrid($quoteGrid);
        $link = \Context::getContext()->link;
        $url = $link->getAdminLink("OutOfStockReminder", true, ["route" => "out_of_stock/create_rule"]);
        $rules = $this->getDoctrine()->getManager()->getRepository(Rule::class)->findAll();

        return $this->render("@Modules/outofstockreminder/views/templates/admin/index.html.twig", compact("url", "rules", "gridView"));

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
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine();

            $rule = new Rule();
            $rule->setTitle($form->get('title')->getData());
            $rule->setThreshold($form->get('threshold')->getData());
            $rule->setStatus($request->request->get("status"));
            $rule->setEmail($form->get('email')->getData());
            $rule->setCategoryId($form->get('category_id')->getData());

            $em->getManager()->persist($rule);
            $em->getManager()->flush();
            $this->addFlash("success", $this->trans("The form has sent", "Modules.OutOfStockReminder.Admin"));
            return $this->redirectToRoute("out_of_stock_rules");
        }else{
            $this->addFlash("error", $this->trans("The form was not validated", "Modules.OutOfStockReminder.Admin"));
            return $this->redirectToRoute("out_of_stock/create_rule");
        }


//        return $this->render("@Modules/outofstockreminder/views/templates/admin/create.html.twig", ["formView" => $form->createView()]);

    }

    public function editAction(int $id){
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(Rule::class)->find($id);
        $form = $this->createForm(RuleType::class, $data);
        $formView = $form->createView();

        return $this->render("@Modules/outofstockreminder/views/templates/admin/edit.html.twig", compact("formView", "id"));
    }

    public function updateAction(int $id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(Rule::class)->find($id);
        $form = $this->createForm(RuleType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash("success", $this->trans("The rule successfully updated", "Modules.OutOfStockReminder.Admin"));
        }

        return $this->redirectToRoute("out_of_stock_rules");
    }

    public function deleteAction(int $id){
        $em = $this->getDoctrine()->getManager();
        $rule = $em->getRepository(Rule::class)->find($id);
        $em->remove($rule);
        $em->flush();
        $this->addFlash("success", $this->trans("The rule successfully Deleted", "Modules.OutOfStockReminder.Admin"));

    }

}