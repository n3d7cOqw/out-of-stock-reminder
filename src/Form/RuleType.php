<?php

namespace OutOfStockReminder\Form;


use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CategoryChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RuleType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $disabledCategories = [];

        $builder
            ->add("title", TextType::class, ["attr" => ["placeholder" => "rule title"], "label" => "Title", "label_attr" => ["form-control-label"]])
            ->add("option", ChoiceType::class, ["choices" => [
                "Product" => 1,
                "Category" => 2
            ], "label" => "Select type"])
            ->add("product", TextType::class, ["attr" => ["placeholder" => "product title"], "label" => "Product", "label_attr" => ["form-control-label"], 'required' => false])
            ->add("select_all_categories", SwitchType::class, [
                'choices' => [
                    '' => 0,
                    ' ' => 1,
                ],
//                "data" => 0,
                ])
            ->add('category_id', CategoryChoiceTreeType::class, [
                'label' => false,
                'disabled_values' => $disabledCategories,
                'required' => false,
                'attr' => ['class' => 'select-all-categories']
            ])
            ->add("clear_categories", ButtonType::class, ['attr' => ['class' => 'btn-primary', ], ])
            ->add("threshold", NumberType::class, ["attr" => ["placeholder" => "threshold"], "label" => "Threshold", "label_attr" => ["form-control-label"]])
            ->add('status', SwitchType::class, [
                'choices' => [
                    'Disable' => 0,
                    'Active' => 1,
                ]
            ])
            ->add("email", TextareaType::class, ["attr" => ["placeholder" => "emails"], "label" => "Email", "label_attr" => ["form-control-label"]]);


        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(["data_class" => null]);
    }


}