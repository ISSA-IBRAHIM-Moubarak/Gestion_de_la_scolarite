<?php

namespace App\Form;

use App\Entity\Inscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditInscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('apprenant')
        ->add('niveau',EntityType::class, array('class' => 'App\Entity\Niveau', 'choice_label' => 'libelleNiveau', 'required' => true))
        ->add('filiere',EntityType::class, array('class' => 'App\Entity\Filiere', 'choice_label' => 'intituleFiliere', 'required' => true))
        ->add('montantVersement')
        ->add('dateVersement')
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Inscription::class,
        ]);
    }
}
