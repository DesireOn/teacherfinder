<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Заглавие на отзива'])
            ->add('content', TextareaType::class, ['label' => 'Съдържание на отзива'])
            ->add('rating', ChoiceType::class, [
                'label' => 'Оценка на на учител (1..5)',
                'choices' => [
                    '-- Моля, изберете оценка --' => null,
                    '1 (Не препоръчвам)' => 1,
                    '2 (Слабо ниво на обучение)' => 2,
                    '3 (Средно ниво на обучение)' => 3,
                    '4 (Добро ниво на обучение)' => 4,
                    '5 (Отлично ниво на обучение)' => 5,
                ]
            ])
            ->add('authorName', TextType::class, ['label' => 'Име и фамилия'])
            ->add('hideAuthorName', CheckboxType::class, ['required' => false, 'label' => 'Желая името ми да бъде скрито', 'attr' => ['class' => 'form-check-input ml-0']])
            ->add('save', SubmitType::class, ['label' => 'Запиши'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
