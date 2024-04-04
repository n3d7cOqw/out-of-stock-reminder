<?php

namespace OutOfStockReminder\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomRadioType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'Yes' => 1,
                'No' => 0,
            ],
            'expanded' => true,
            'multiple' => false,
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}