<?php

namespace App\Form;

use App\Entity\MatiereSemestre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatiereSemestreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->semestre = $options['semestre'];
        
        $builder
            ->add('matiere',EntityType::class, array('class' => 'App\Entity\Matiere', 'choice_label' => 'intituleMatiere', 'required' => false,
            'multiple' => true,
            ))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MatiereSemestre::class,
            'semestre' => null,
        ]);
    }
}
