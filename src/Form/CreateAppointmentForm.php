<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CreateAppointmentForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', TextType::class, [
                 'constraints' => [
                    new NotBlank(),
                 ]
            ])
            ->add('status', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Choice(['scheduled', 'confirmed', 'cancelled'])
                ]
            ])
            ->add('notes', TextType::class, [
                'required' => false
            ])
            ->add('vehicule_id', TextType::class, [
                'constraints' => [new NotBlank()]
            ])
            ->add('garage_id', TextType::class, [
                'constraints' => [new NotBlank()]
            ])
            ->add('operations', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null
        ]);
    }
}
