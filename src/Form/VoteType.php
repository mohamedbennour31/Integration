<?php

namespace App\Form;

use App\Entity\Vote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Hackathon;
use App\Entity\Projet;
use App\Entity\Evaluation;

class VoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idEvaluation', EntityType::class, [
                'class' => Evaluation::class,
                'choice_label' => function (Evaluation $evaluation) {
                    return $evaluation->getId(); // or any readable representation
                },
                'placeholder' => 'Select an evaluation',
            ])
            ->add('idHackathon', EntityType::class, [
                'class' => Hackathon::class,
                'choice_label' => function (Hackathon $hackathon) {
                    return $hackathon->getId(); // or $hackathon->getName() if exists
                },
                'placeholder' => 'Select a hackathon',
            ])
            ->add('idProjet', EntityType::class, [
                'class' => Projet::class,
                'choice_label' => function (Projet $projet) {
                    return $projet->getId(); // or $projet->getTitle() if exists
                },
                'placeholder' => 'Select a project',
            ])
            ->add('idVotant')
            ->add('valeurVote')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vote::class,
        ]);
    }
}
