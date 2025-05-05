<?php

namespace App\Form;

use App\Entity\Communaute;
use App\Entity\Hackathon;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommunauteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la communauté',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'rows' => 4
                ]
            ])
            ->add('idHackathon', EntityType::class, [
                'class' => Hackathon::class,
                'choice_label' => 'nom_hackathon',
                'label' => 'Hackathon associé',
                'required' => false,
                'placeholder' => 'Aucun hackathon',
                'property_path' => 'id_hackathon',
                'disabled' => true,
                'attr' => [
                    'class' => 'form-control disabled',
                    'readonly' => true
                ]
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
                'data' => true, 
                'property_path' => 'is_active',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Communaute::class,
        ]);
    }
} 