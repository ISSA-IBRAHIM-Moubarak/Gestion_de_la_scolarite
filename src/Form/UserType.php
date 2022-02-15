<?php

namespace App\Form;
use App\Entity\User;
use Doctrine\DBAL\Types\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType as TypeDateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class UserType extends AbstractType
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
            ->add('roles', ChoiceType::class, array(
                'choices' => array(
                    'Directeur' => 'ROLE_DIRECTEUR',
                    'Censeur' => 'ROLE_CENSEUR',
                    'Surveillant' => 'ROLE_SURVEILLANT',
                    'Caissier' => 'ROLE_CAISSIER',
                    'Gérant-Cantine' => 'ROLE_GERANT_CANTINE',
                    'Gérant-Transport' => 'ROLE_GERANT_TRANPORT',
                    'Enseignant' => 'ROLE_ENSEIGNANT',
                    'Utilisateur' => 'ROLE_USER',
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
