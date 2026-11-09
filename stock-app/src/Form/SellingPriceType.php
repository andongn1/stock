<?php

namespace App\Form;

use App\Entity\SellingPrice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SellingPriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price_ttc', MoneyType::class, [
                'label' => 'Prix TTC',
                'currency' => 'EUR',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('sale_type', ChoiceType::class, [
                'label' => 'Type de vente',
                'choices' => [
                    'Par unite' => 'per_unit',
                    'Par conditionnement' => 'per_packaging',
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantite',
                'required' => false,
                'attr' => ['class' => 'form-control', 'step' => '0.0001'],
            ])
            ->add('currency', TextType::class, [
                'label' => 'Devise',
                'data' => 'EUR',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SellingPrice::class,
        ]);
    }
}
