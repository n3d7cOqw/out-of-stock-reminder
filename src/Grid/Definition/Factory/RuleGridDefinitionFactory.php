<?php

namespace OutOfStockReminder\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SubmitGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\NumberMinMaxFilterType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RuleGridDefinitionFactory extends AbstractGridDefinitionFactory
{

    protected function getId()
    {
        return "rules";
    }

    protected function getName()
    {
        return $this->trans('Rules', [], 'Modules.OutOfStockReminder.Admin');
    }

//1
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new DataColumn('id'))
                ->setName($this->trans('ID', [], 'Modules.OutOfStockReminder.Admin'))
                ->setOptions([
                    'field' => 'id',
                ])
            )
            ->add((new DataColumn('title'))
                ->setName($this->trans('Title', [], 'Modules.OutOfStockReminder.Admin'))
                ->setOptions([
                    'field' => 'title',
                ])
            )
            ->add((new DataColumn('threshold'))
                ->setName($this->trans('Threshold', [], 'Modules.OutOfStockReminder.Admin'))
                ->setOptions([
                    'field' => 'threshold',
                ])
            )
            ->add((new DataColumn('status'))
                ->setName($this->trans('Status', [], 'Modules.OutOfStockReminder.Admin'))
                ->setOptions([
                    'field' => 'status',
                ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new LinkRowAction('edit'))
                                    ->setName('Edit')
                                    ->setIcon('edit')
                                    ->setOptions([
                                        'route' => 'out_of_stock_edit',
                                        'route_param_name' => 'id',
                                        'route_param_field' => 'id',
                                        // A click on the row will have the same effect as this action
                                        'clickable_row' => true,
                                    ])
                            )
                            ->add(
                                (new SubmitRowAction('delete'))
                                    ->setName('Delete')
                                    ->setIcon('delete')
                                    ->setOptions([
                                        'confirm_message' => 'Delete selected item?',
                                        'route' => 'out_of_stock_delete',
                                        'route_param_name' => 'id',
                                        'route_param_field' => 'id',
                                        "method" => "POST"

                                    ])
                            )
                    ])
            );
    }

//    protected function getGridActions()
//    {
//        return (new GridActionCollection())
//            ->add(
//                (new SubmitGridAction('out_of_stock_delete'))
//                    ->setName('Delete')
//                    ->setIcon('delete')
//                    ->setOptions([
//                        'submit_route' => 'out_of_stock_delete',
//                        'confirm_message' => 'Are you sure?',
//                    ])
//            );
//    }


    public function getFilters(): FilterCollection
    {
        return new FilterCollection();
    }

}


