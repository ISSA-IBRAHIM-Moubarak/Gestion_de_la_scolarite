<?php

namespace App\Form;

use App\Entity\Niveau;
use App\Repository\SalleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoixNiveauType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $this->user = $options['user'];
        $builder
           
            ->add('salle',EntityType::class, array('class' => 'App\Entity\Salle', 'choice_label' => 'intituleSalle', 'required' => true,
            'query_builder' => function(\App\Repository\SalleRepository $salleRepository){

                return $salleRepository->createQueryBuilder('n')
                ->where('n.structure = :structure')
                ->setParameter('structure',$this->user->getStructure()->getId());
            }
           
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'user' => null
        ]);
    }
}
