<?php

namespace OutOfStockReminder\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SubmitGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\NumberMinMaxFilterType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RuleGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'rules';

    protected function getId()
    {
        return self::GRID_ID;
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
            ->add((new ToggleColumn('status'))
                ->setName($this->trans('Enabled', [], 'Modules.OutOfStockReminder.Admin'))
                ->setOptions([
                    'field' => 'status',
                    'primary_field' => 'id',
                    'route' => "out_of_stock/toggle_status",
                    'route_param_name' => 'id',

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


    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('ID', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('id')
            )
            ->add(
                (new Filter('title', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Name', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('title')
            )
            ->add(
                (new Filter('threshold', NumberMinMaxFilterType::class, [
                    'min_field_options' => [
                        'attr' => [
                            'placeholder' => $this->trans('Min', [], 'Admin.Global'),
                        ],
                    ],
                    'max_field_options' => [
                        'attr' => [
                            'placeholder' => $this->trans('Max', [], 'Admin.Global'),
                        ],
                    ],
                ]))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Threshold', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('threshold')
            )
            ->add(
                (new Filter('status', YesAndNoChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        "choices" => ["Active" => 1, "Disabled" => 0]
                    ])
                    ->setAssociatedColumn('status')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'out_of_stock_rules',
                    ])
                    ->setAssociatedColumn('actions')
            )
            ;
    }

}


