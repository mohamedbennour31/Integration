<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer votre nom'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer votre email'
                    ]),
                    new Assert\Email([
                        'message' => 'L\'email {{ value }} n\'est pas un email valide'
                    ])
                ]
            ])
            ->add('phone', TelType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer votre numéro de téléphone'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]{8}$/',
                        'message' => 'Le numéro de téléphone doit contenir exactement 8 chiffres'
                    ])
                ]
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez choisir une date'
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date doit être aujourd\'hui ou ultérieure'
                    ])
                ]
            ])
            ->add('time', TimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez choisir une heure'
                    ])
                ]
            ])
            ->add('people', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez indiquer le nombre de personnes'
                    ]),
                    new Assert\Range([
                        'min' => 1,
                        'max' => 20,
                        'minMessage' => 'Le nombre minimum de personnes est {{ limit }}',
                        'maxMessage' => 'Le nombre maximum de personnes est {{ limit }}'
                    ])
                ]
            ])
            ->add('message', TextareaType::class, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'attr' => ['class' => 'php-email-form']
        ]);
    }
}
