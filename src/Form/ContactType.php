<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer votre nom'
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Votre nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre nom ne peut pas dépasser {{ limit }} caractères'
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
            ->add('subject', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un sujet'
                    ])
                ]
            ])
            ->add('message', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer votre message'
                    ]),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'Votre message doit contenir au moins {{ limit }} caractères'
                    ])
                ]
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
