<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('brand', TextType::class, [
                'label' => 'Marque',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('family', EntityType::class, [
                'label' => 'Famille',
                'class' => 'App\Entity\Family',
                'choice_label' => 'name',
                'choice_value' => 'id',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('subFamily', EntityType::class, [
                'label' => 'Sous-Famille',
                'class' => 'App\Entity\SubFamily',
                'choice_label' => 'name',
                'choice_value' => 'id',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('unit', EntityType::class, [
                'label' => 'Unite',
                'class' => 'App\Entity\Unit',
                'choice_label' => 'name',
                'choice_value' => 'id',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('packaging', EntityType::class, [
                'label' => 'Conditionnement',
                'class' => 'App\Entity\Packaging',
                'choice_label' => 'name',
                'choice_value' => 'id',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('packaging_unit_quantity', NumberType::class, [
                'label' => 'Quantite par conditionnement',
                'required' => false,
                'attr' => ['class' => 'form-control', 'step' => '0.01'],
            ])
            ->add('vat', EntityType::class, [
                'label' => 'TVA',
                'class' => 'App\Entity\Vat',
                'choice_label' => function($vat) {
                    return $vat->getLabel() . ' (' . $vat->getRate() . '%)';
                },
                'choice_value' => 'id',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
