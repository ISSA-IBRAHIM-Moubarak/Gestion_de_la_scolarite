<?php

namespace App\Form;

use App\Entity\EmploiTemps;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditEmploiTempsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('LibelleEmploiTemps')
            ->add('DateDebutEmploiTemps')
            ->add('DateFinEmploiTemps')
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EmploiTemps::class,
        ]);
    }
}
