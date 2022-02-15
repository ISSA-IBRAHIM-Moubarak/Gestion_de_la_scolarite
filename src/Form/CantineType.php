<?php

namespace App\Form;

use App\Entity\Cantine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CantineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('montantCantine', IntegerType::class,[
                'required' => true,
                'constraints' => [new Positive()],
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('DateDebutCantine',DateType::class, array('label' => 'Date début',
            'widget' => 'single_text',
            'required' => false))
            ->add('DateFinCantine',DateType::class, array('label' => 'Date début',
            'widget' => 'single_text',
            'required' => false))
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cantine::class,
        ]);
    }
}
