<?php

namespace App\Form;

use App\Entity\Family;
use App\Entity\SubFamily;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SubFamilyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(message: 'Le nom de la sous-famille est obligatoire.'),
                    new Length(max: 255, maxMessage: 'Le nom ne peut pas depasser 255 caracteres.'),
                ],
            ])
            ->add('family', EntityType::class, [
                'label' => 'Famille',
                'class' => Family::class,
                'choice_label' => 'name',
                'placeholder' => 'Selectionnez une famille',
                'constraints' => [
                    new NotBlank(message: 'La famille est obligatoire.'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubFamily::class,
        ]);
    }
}
