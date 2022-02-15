<?php

namespace App\Form;

use App\Entity\Matiere;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Component\Form\AbstractType;
use phpDocumentor\Reflection\Types\Float_;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class MatiereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('intituleMatiere')
        ->add('coeficient', IntegerType::class,[
            'required' => true,
            'constraints' => [new Positive()],
            'attr' => [
                'min' => 1
            ]
        ])
        ->add('nombreHeure',IntegerType::class,[
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
