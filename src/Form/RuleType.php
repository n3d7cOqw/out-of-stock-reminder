<?php

namespace OutOfStockReminder\Form;


use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CategoryChoiceTreeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class RuleType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $disabledCategories = [];


        $builder
            ->add("title", TextType::class, ["attr" => ["placeholder" => "rule title"], "label" => "Title", "label_attr" => ["form-control-label"]])
            ->add("product", TextType::class, ["attr" => ["placeholder" => "product title"], "label" => "Product", "label_attr" => ["form-control-label"], 'required' => false])
            ->add('category_id', CategoryChoiceTreeType::class, [
                'label' => false,
                'disabled_values' => $disabledCategories,
                'required' => false,
                'attr' => ['class' => 'select-all-categories']
            ])
            ->add("threshold", NumberType::class, ["attr" => ["placeholder" => "threshold"], "label" => "Threshold", "label_attr" => ["form-control-label"] ])
            ->add("email", TextareaType::class, ["attr" => ["placeholder" => "emails"], "label" => "Email", "label_attr" => ["form-control-label"]])
            ->add("save", SubmitType::class);

        parent::buildForm($builder, $options);
    }
}