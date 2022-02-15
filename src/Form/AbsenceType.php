<?php

namespace App\Form;

use App\Entity\Absence;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Component\Form\AbstractType;
use phpDocumentor\Reflection\Types\Float_;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType as TypeDateType;


class AbsenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('motif')
            ->add('module',EntityType::class, array('class' => 'App\Entity\Module','choice_label' => 'intituleModule' , 'required' => true))
            ->add('apprenant',EntityType::class,array('class' => 'App\Entity\Apprenant','choice_label' => 'NomApprenant'  ,'required' => true))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Absence::class,
        ]);
    }
}
