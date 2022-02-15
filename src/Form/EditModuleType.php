<?php

namespace App\Form;

use App\Entity\Module;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class EditModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('intituleModule')
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
            'data_class' => Module::class,
        ]);
    }
}
