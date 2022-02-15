<?php

namespace App\Form;

use App\Entity\Versement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtatIntervalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('dateFrom',DateType::class, array('label' => 'Date début',
        'widget' => 'single_text',
        'mapped' => false,
        'required' => false))
        ->add('dateTo',DateType::class, array('label' => 'Date fin',
        'widget' => 'single_text',
        'mapped' => false,
        'required' => false))
        ->add('niveau',EntityType::class, array('class' => 'App\Entity\Niveau', 'choice_label' => 'libelleNiveau', 'required' => false, 'placeholder' => 'Tous les niveaux'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
