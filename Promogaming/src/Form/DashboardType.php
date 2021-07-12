<?php

namespace App\Form;

use App\Entity\User;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class DashboardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    { 
        $builder     
            ->add('username', null,['help'=>'Le nom d\'utilisateur doit faire entre 3 et 20 caractères au maximum'])
            ->add('roles', ChoiceType :: class, 
            ['choices' => ['Admin' => 'ROLE_ADMIN',
                        ],
                'multiple' => true,
                'expanded' =>true,
            ])
            ->add('password',PasswordType ::class,['help'=>'le mot de passe doit faire 3 caractères au minimum'])
            ->add('email')
             ;
           
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => User::class,]);
    }
}
