<?php

namespace App\Form;

use App\Entity\Note;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('inscription',EntityType::class,array('class' => 'App\Entity\Inscription', 'choice_label' => 'niveau', 'required' => true))
//            ->add('inscription',EntityType::class, array('class' => 'App\Entity\Inscription', 'choice_label' => 'apprenant', 'required' => true))
            ->add('noteApprenant',FloatType::class)
            ->add('tevaluation',EntityType::class,array('class' => 'App\Entity\Tevaluation', 'choice_label' => 'typeEvaluation', 'required' => true))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
