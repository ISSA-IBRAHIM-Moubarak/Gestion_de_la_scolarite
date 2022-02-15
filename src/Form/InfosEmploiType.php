<?php

namespace App\Form;

use App\Entity\InfosEmploi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class InfosEmploiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('heureDebut')
            ->add('heureFin')
            ->add('periode', ChoiceType::class, array(
                'choices' => array(
                    'Lundi' => 'Lundi',
                    'Mardi' => 'Mardi',
                    'Mercredi' => 'Mercredi',
                    'Jeudi' => 'Jeudi',
                    'Vendredi' => 'Vendredi',
                    'Samedi' => 'Samedi',
                ),
                'required' => true
            ))
            ->add('module',EntityType::class, array('class' => 'App\Entity\Module', 'choice_label' => 'intituleModule', 'required' => true))
            ->add('matiere',EntityType::class, array('class' => 'App\Entity\Matiere', 'choice_label' => 'intituleMatiere', 'required' => true))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InfosEmploi::class,
        ]);
    }
}
