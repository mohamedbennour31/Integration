<?php

namespace App\Form;

use App\Entity\Hackathon;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class HackathonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            ->add('nom_hackathon')
            ->add('description')
            ->add('date_debut', null, [
                'widget' => 'single_text'
            ])
            ->add('date_fin', null, [
                'widget' => 'single_text'
            ])
            ->add('lieu')
            ->add('theme')
            ->add('max_participants')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hackathon::class,
        ]);
    }
}
