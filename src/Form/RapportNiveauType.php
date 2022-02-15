<?php

namespace App\Form;

use App\Entity\Inscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class RapportNiveauType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('niveau',EntityType::class, array(
                'class' => 'App\Entity\Niveau',
                'choice_label' => 'libelleNiveau',
                'required' => false,
                'placeholder' => 'Tous les niveaux'
            ))
            ->add('bourse',EntityType::class, array(
                'class' => 'App\Entity\Bourse',
                'choice_label' => 'libelleBourse',
                'required' => false,
                'placeholder' => 'Toutes les bourses'

            ))
            ->add('annee',EntityType::class, array(
                'class' => 'App\Entity\Annee',
                'choice_label' => 'libelleAnneeScolaire',
                'required' => false,
                'placeholder' => 'Toutes les années'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
