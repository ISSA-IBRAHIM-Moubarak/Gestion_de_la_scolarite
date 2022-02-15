<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class EditSuperAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('username',TextType::class,array('attr' => array('readonly' => true)))
        ->add('telephone')
        ->add('email')
        ->add('firstName')
        ->add('lastName')
        ->add('structure')
        ->add('roles', ChoiceType::class, array(
            'choices' => array(
                'Admin niveau supérieur' => 'ROLE_ADMIN_SUPERIEUR',
                'Admin niveau secondaire' => 'ROLE_ADMIN_SECONDAIRE',
                'Admin niveau primaire' => 'ROLE_ADMIN_PRIMAIRE',
            ),
            'multiple' => true,
            'expanded' => true,
            'mapped' => true,
            'label' => 'Rôles',
        ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
