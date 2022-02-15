<?php

namespace App\Form;

use App\Entity\Apprenant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EditApprenantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('NomApprenant')
            ->add('PrenomApprenant')
            ->add('DateNaissance',DateType::class, array('label' => 'Date début',
            'widget' => 'single_text',
            'required' => false))
            ->add('LieuNaissance')
            ->add('Nationalite')
            ->add('Genre',ChoiceType::class,array('choices' => array('M' => 'Masculin', 'F' => 'Feminin'), 'required' => true))
            ->add('Contact')
            ->add('Email')
            ->add('Adresse')
            ->add('SituationMatrimoniale',ChoiceType::class,array('choices' => array('Celibataire' => 'Celibataire', 'Marié(e)' => 'Marié(e)'), 'required' => true))

           
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Apprenant::class,
        ]);
    }
}
