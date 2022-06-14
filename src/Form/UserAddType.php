<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Имейл'])
            ->add('password', PasswordType::class, ['label' => 'Парола'])
            ->add('roles', ChoiceType::class, ['label' => 'Роля', 'multiple' => true, 'choices' => [
                    'Модератор' => 'ROLE_MODERATOR',
                    'Администратор' => 'ROLE_ADMIN',
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Добави'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
