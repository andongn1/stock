<?php

namespace App\Form;

use App\Entity\PriceHistory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceHistoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('article', EntityType::class, [
                'label' => 'Article',
                'class' => 'App\Entity\Article',
                'choice_label' => 'name',
                'choice_value' => 'id',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('supplier', EntityType::class, [
                'label' => 'Fournisseur',
                'class' => 'App\Entity\Supplier',
                'choice_label' => 'name',
                'choice_value' => 'id',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'EUR',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('currency', TextType::class, [
                'label' => 'Devise',
                'data' => 'EUR',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('price_type', ChoiceType::class, [
                'label' => 'Type de prix',
                'choices' => [
                    'Par unite' => 'per_unit',
                    'Par conditionnement' => 'per_packaging',
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('valid_from', DateType::class, [
                'label' => 'Valide a partir du',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('valid_to', DateType::class, [
                'label' => 'Valide jusqu\'au',
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PriceHistory::class,
        ]);
    }
}
