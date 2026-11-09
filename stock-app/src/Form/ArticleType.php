<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Family;
use App\Entity\Packaging;
use App\Entity\SubFamily;
use App\Entity\Unit;
use App\Entity\Vat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $attrClass = ['class' => 'block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => $attrClass,
                'constraints' => [
                    new NotBlank(message: 'Le nom de l\'article est obligatoire.'),
                ],
            ])
            ->add('brand', TextType::class, [
                'label' => 'Marque',
                'required' => false,
                'attr' => $attrClass,
            ])
            ->add('family', EntityType::class, [
                'label' => 'Famille',
                'class' => Family::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'placeholder' => 'Sélectionnez une famille',
                'attr' => $attrClass,
                'constraints' => [
                    new NotBlank(message: 'La famille est obligatoire.'),
                ],
            ])
            ->add('subFamily', EntityType::class, [
                'label' => 'Sous-famille',
                'class' => SubFamily::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'placeholder' => 'Sélectionnez une sous-famille',
                'attr' => $attrClass,
                'constraints' => [
                    new NotBlank(message: 'La sous-famille est obligatoire.'),
                ],
            ])
            ->add('unit', EntityType::class, [
                'label' => 'Unité',
                'class' => Unit::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'placeholder' => 'Sélectionnez une unité',
                'attr' => $attrClass,
                'constraints' => [
                    new NotBlank(message: 'L\'unité est obligatoire.'),
                ],
            ])
            ->add('packaging', EntityType::class, [
                'label' => 'Conditionnement',
                'class' => Packaging::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'placeholder' => 'Sélectionnez un conditionnement',
                'attr' => $attrClass,
                'constraints' => [
                    new NotBlank(message: 'Le conditionnement est obligatoire.'),
                ],
            ])
            ->add('packagingUnitQuantity', NumberType::class, [
                'label' => 'Quantité unitaire',
                'required' => false,
                'attr' => $attrClass,
            ])
            ->add('vat', EntityType::class, [
                'label' => 'TVA',
                'class' => Vat::class,
                'choice_label' => 'label',
                'choice_value' => 'id',
                'placeholder' => 'Sélectionnez une TVA',
                'attr' => $attrClass,
                'constraints' => [
                    new NotBlank(message: 'La TVA est obligatoire.'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
