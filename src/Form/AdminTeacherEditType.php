<?php

namespace App\Form;

use App\Entity\Teacher;
use App\Form\DataTransformer\CityToNumberTransformer;
use App\Form\DataTransformer\SubjectToNumberTransformer;
use App\Repository\CityRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminTeacherEditType extends AbstractType
{
    private $subjects;

    private $cities;

    private $entityManager;

    private $subjectRepository;

    private $cityRepository;

    private $subjectToNumberTransformer;

    private $cityToNumberTransformer;

    /**
     * @param SubjectRepository $subjectRepository
     * @param CityRepository $cityRepository
     * @param EntityManagerInterface $entityManager
     * @param SubjectToNumberTransformer $subjectToNumberTransformer
     * @param CityToNumberTransformer $cityToNumberTransformer
     */
    public function __construct(
        SubjectRepository $subjectRepository,
        CityRepository $cityRepository,
        EntityManagerInterface $entityManager,
        SubjectToNumberTransformer $subjectToNumberTransformer,
        CityToNumberTransformer $cityToNumberTransformer
    )
    {
        $subjects = $subjectRepository->findAll();
        foreach ($subjects as $subject) {
            $this->subjects[$subject->getName()] = $subject->getId();
        }

        $cities = $cityRepository->findAll();
        foreach ($cities as $city) {
            $this->cities[$city->getName()] = $city->getId();
        }
        $this->entityManager = $entityManager;
        $this->subjectRepository = $subjectRepository;
        $this->cityRepository = $cityRepository;
        $this->subjectToNumberTransformer = $subjectToNumberTransformer;
        $this->cityToNumberTransformer = $cityToNumberTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Име'])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => 'active',
                    'Inactive' => 'inactive',
                    'Pending' => 'pending',
                ],
                'label' => 'Статус'
            ])
            ->add('rating', NumberType::class, ['label' => 'Оценка', 'disabled' => true])
            ->add('email', EmailType::class, ['label' => 'Имейл'])
            ->add('phone', NumberType::class, ['label' => 'Телефонен номер'])
            ->add('description', TextareaType::class, ['label' => 'Описание', 'required' => false])
            ->add('pricePerHour', NumberType::class, ['label' => 'Цена за час'])
            ->add('gender', ChoiceType::class, [
                'label' => 'Пол',
                'choices' => [
                    'Мъж' => 'm',
                    'Жена' => 'f',
                ]
            ])
            ->add('subject', ChoiceType::class, [
                'label' => 'Предмет',
                'choices' => $this->subjects
            ])
            ->add('city', ChoiceType::class, [
                'label' => 'Град',
                'choices' => $this->cities,
            ])
            ->add('save', SubmitType::class, ['label' => 'Редактирай'])
        ;

        $builder->get('subject')
            ->addModelTransformer($this->subjectToNumberTransformer);
        $builder->get('city')
            ->addModelTransformer($this->cityToNumberTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Teacher::class,
        ]);
    }
}
