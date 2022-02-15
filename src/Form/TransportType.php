<?php

namespace App\Form;

use App\Entity\Transport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class TransportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('montantTransport', IntegerType::class,[
                'required' => true,
                'constraints' => [new Positive()],
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('intitileTransport')
            ->add('DateDebutTransport',DateType::class, array('label' => 'Date début',
            'widget' => 'single_text',
            'required' => false))
            ->add('DateFinTransport',DateType::class, array('label' => 'Date fin',
            'widget' => 'single_text',
            'required' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transport::class,
        ]);
    }
}
