<?php

namespace App\Form;

use App\Entity\Emploi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class EmploiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut',DateType::class, array('label' => 'Date début',
            'widget' => 'single_text',
            'required' => false))
            ->add('dateFin',DateType::class, array('label' => 'Date début',
            'widget' => 'single_text',
            'required' => false))
            ->add('libelleEmploi')

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Emploi::class,
        ]);
    }
}
