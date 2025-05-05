<?php

namespace App\Form;

use App\Entity\Hackathon;
use App\Entity\Projets;
use App\Entity\Technologies;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('statut')
            ->add('priorite')
            ->add('description')
            ->add('ressource')
            ->add('technologies', EntityType::class, [
                'class' => Technologies::class,
                'choice_label' => 'nom_tech',
                'multiple' => true,
            ])
            ->add('id_hack', EntityType::class, [
                'class' => Hackathon::class,
                'choice_label' => 'nom_hackathon',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projets::class,
        ]);
    }
}
