<?php

namespace OutOfStockReminder\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

class OutOfStockReminderConfigurationController extends FrameworkBundleAdminController
{
    public function index(){

        $url = $this->getContext()->link->getModuleLink("outofstockreminder", "email");
        $path = realpath("../");
        $layoutTitle = $this->trans('Configuration', 'Modules.OutOfStockReminder.Admin');

        return $this->render('@Modules/outofstockreminder/views/templates/admin/configuration.html.twig', compact('url', "layoutTitle", "path"));

    }
}