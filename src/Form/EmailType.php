<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType as SymfonyEmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('recipient_email', SymfonyEmailType::class, [
        'label' => 'Recipient Email',
        'attr' => [
          'class' => 'form-control-lg',
          'placeholder' => 'recipient@example.com'
        ]
      ])
      ->add('subject', TextType::class, [
        'label' => 'Subject',
        'attr' => [
          'class' => 'form-control-lg'
        ]
      ])
      ->add('body', TextareaType::class, [
        'label' => 'Message',
        'attr' => [
          'class' => 'form-control',
          'rows' => 8,
          'style' => 'min-height: 200px;'
        ]
      ]);
  }
}
