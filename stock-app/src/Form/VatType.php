<?php

namespace App\Form;

use App\Entity\Vat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as Unique;

class VatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rate', NumberType::class, [
                'label' => 'Taux',
                'scale' => 2,
                'constraints' => [
                    new NotBlank(message: 'Le taux de TVA est obligatoire.'),
                ],
            ])
            ->add('label', TextType::class, [
                'label' => 'Libelle',
                'constraints' => [
                    new NotBlank(message: 'Le libelle est obligatoire.'),
                    new Length(max: 255),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vat::class,
        ]);
    }
}
