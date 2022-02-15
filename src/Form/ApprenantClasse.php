<?php

namespace App\Form;

use App\Entity\ApprenantClasse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApprenantClasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('salle',EntityType::class, array('class' => 'App\Entity\Salle', 'choice_label' => 'intituleSalle', 'required' => true))
   //->add('inscription',EntityType::class, array('class' => 'App\Entity\Inscription', 'choice_label' => 'apprenant', 'required' => true))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ApprenantClasse::class,
        ]);
    }
}
