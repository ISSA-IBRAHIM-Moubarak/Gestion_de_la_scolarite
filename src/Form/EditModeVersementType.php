<?php

namespace App\Form;

use App\Entity\ModeVersement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EditModeVersementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fraisInscription')
            ->add('typeVersement',ChoiceType::class,array('choices' => array('Liquide' => 'Liquide', 'Carte Bancaire' => 'Carte Bancaire'), 'required' => true))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ModeVersement::class,
        ]);
    }
}
