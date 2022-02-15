<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class SuperAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('username')
        ->add('telephone')
        ->add('email')
        ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Mot de passe incorret !.',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'first_options'  => ['label' => 'Mot de passe'],
            'second_options' => ['label' => 'Configuration du Mot de passe'],
        ])
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
