<?php

namespace OutOfStockReminder\Controller\Admin;

use OutOfStockReminder\Entity\Rule;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class EmailController extends FrameworkBundleAdminController
{
    public function sendMails(){
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $query = $qb->select("r")
            ->from(Rule::class, "r")
            ->where("r.status = " . 1 )
            ->where($qb->expr()->isNull("r.product_id"))
            ->getQuery();
        dd($query->getResult());
    }
}