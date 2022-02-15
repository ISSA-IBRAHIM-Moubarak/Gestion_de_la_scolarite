<?php

namespace App\Form;

use App\Entity\Matiere;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class EditMatiereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('intituleMatiere')
            ->add('coeficient', IntegerType::class,[
                'mapped' => false,
                'required' => true,
                'constraints' => [new Positive()],
                'attr' => [
                    'min' => 1
                ]
            ])
            ->add('nombreHeure',IntegerType::class,[
                'mapped' => false,
                'required' => true,
                'constraints' => [new Positive()],
                'attr' => [
                    'min' => 1
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Matiere::class,
        ]);
    }
}
