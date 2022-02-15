<?php

namespace App\Form;

use App\Entity\Inscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           
            ->add('niveau',EntityType::class, array('class' => 'App\Entity\Niveau', 'choice_label' => 'libelleNiveau', 'required' => true))
            ->add('filiere',EntityType::class, array('class' => 'App\Entity\Filiere', 'choice_label' => 'intituleFiliere', 'required' => true))
            ->add('bourse',EntityType::class, array('class' => 'App\Entity\Bourse', 'choice_label' => 'libelleBourse', 'required' => true))
            ->add('montantInscription')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Inscription::class,
        ]);
    }
}
