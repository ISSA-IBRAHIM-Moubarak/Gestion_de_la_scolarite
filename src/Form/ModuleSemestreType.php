<?php

namespace App\Form;

use App\Entity\ModuleSemestre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModuleSemestreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->semestre = $options['semestre'];
        
        $builder
            ->add('module',EntityType::class, array('class' => 'App\Entity\Module', 'choice_label' => 'intituleModule', 'required' => false,
            'multiple' => true,
            ))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ModuleSemestre::class,
            'semestre' => null,
        ]);
    }
}
