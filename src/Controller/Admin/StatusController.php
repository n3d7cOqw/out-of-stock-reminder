<?php

namespace OutOfStockReminder\Controller\Admin;

use OutOfStockReminder\Entity\Rule;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tools;


class StatusController extends FrameworkBundleAdminController
{
    public function toggleStatus($id): JsonResponse{
        $em = $this->getDoctrine()->getManager();
        $rule = $em->getRepository(Rule::class)->find($id);
        $rule->setStatus((int) !$rule->getStatus());
        $em->persist($rule);
        $em->flush();
        if ($rule->getId() !== null) {
            return $this->json( (array(
                'success' => 1,
                'text' => $this->trans('Status updated successfully!', "Modules.OutOfStockReminder.Admin")
            )));
        } else {
            return $this->json(array(
                'success' => 0,
                'text' => $this->trans('Something went wrong!', "Modules.OutOfStockReminder.Admin")
            ));
        }

    }
}