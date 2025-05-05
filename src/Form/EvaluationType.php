<?php

namespace App\Form;

use App\Entity\Evaluation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Jury;
use App\Entity\Hackathon;
use App\Entity\Projet;

class EvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idJury', EntityType::class, [
                'class' => Jury::class,
                'choice_label' => 'nom', // Or something more readable like 'nom'
                'placeholder' => 'Choose a jury',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('idHackathon', EntityType::class, [
                'class' => Hackathon::class,
                'choice_label' => 'id', // Or maybe 'title'
                'placeholder' => 'Choose a hackathon',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('idProjet', EntityType::class, [
                'class' => Projet::class,
                'choice_label' => 'id', // Or 'nom'/'titre' if available
                'placeholder' => 'Choose a project',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('noteTech')
            ->add('noteInnov')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evaluation::class,
        ]);
    }
}
