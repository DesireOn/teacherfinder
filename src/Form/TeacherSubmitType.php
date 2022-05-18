<?php

namespace App\Form;

use App\Entity\LessonType;
use App\Entity\Teacher;
use App\Repository\CityRepository;
use App\Repository\SubjectRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class TeacherSubmitType extends AbstractType
{
    private $subjects;

    private $cities;

    private $entityManager;

    private $subjectRepository;

    private $cityRepository;

    /**
     * @param SubjectRepository $subjectRepository
     * @param CityRepository $cityRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        SubjectRepository $subjectRepository,
        CityRepository $cityRepository,
        EntityManagerInterface $entityManager
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
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Име'])
            ->add('email', EmailType::class, ['label' => 'Имейл'])
            ->add('phone', NumberType::class, ['label' => 'Телефонен номер'])
            ->add('description', TextareaType::class, ['label' => 'Описание', 'required' => false])
            ->add('logo', FileType::class, [
                'label' => 'Профилна снимка',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Моля качете валидно изображение.'
                    ])
                ]
            ])
            ->add('pricePerHour', NumberType::class, ['label' => 'Цена за час'])
            ->add('gender', ChoiceType::class, [
                'label' => 'Пол',
                'choices' => [
                    'Мъж' => 'm',
                    'Жена' => 'f',
                    'Не казвам' => null
                ]
            ])
            ->add('lessonTypes', ChoiceType::class, [
                'label' => 'Начини на обучение',
                'choices' => [
                    'Онлайн' => 'online',
                    'Присъствено' => 'in-person'
                ],
                'multiple' => true,
                'mapped' => false
            ])
            ->add('subject', ChoiceType::class, [
                'label' => 'Предмет',
                'choices' => $this->subjects,
                'mapped' => false
            ])
            ->add('city', ChoiceType::class, [
                'label' => 'Град',
                'choices' => $this->cities,
                'mapped' => false
            ])
            ->addEventListener(
                FormEvents::SUBMIT,
                [$this, 'submit']
            )
            ->add('save', SubmitType::class, ['label' => 'Регистрирай се'])
        ;
    }

    public function submit(FormEvent $event)
    {
        $teacher = $event->getData();
        $form = $event->getForm();

        if ($teacher instanceof Teacher) {
            $teacher->setCreatedAt(new DateTimeImmutable('now'));
            $teacher->setStatus('pending');
            $teacher->setRating(0);
            $teacher->setActiveReviewsCount(0);

            $lessonTypes = $form->get('lessonTypes')->getData();
            if (!empty($lessonTypes)) {
                foreach ($lessonTypes as $lessonType) {
                    if ($lessonType === 'online' || $lessonType === 'in-person') {
                        $lessonTypeEntity = new LessonType();
                        $lessonTypeEntity->setType($lessonType);
                        $lessonTypeEntity->setTeacher($teacher);
                        $this->entityManager->persist($lessonTypeEntity);

                        $teacher->addLessonType($lessonTypeEntity);
                        $this->entityManager->persist($teacher);
                    }
                }
            }

            $subject = $form->get('subject')->getData();
            if (is_numeric($subject)) {
                $subjectEntity = $this->subjectRepository->findOneBy(['id' => $subject]);
                if (!is_null($subjectEntity)) {
                    $teacher->setSubject($subjectEntity);
                    $this->entityManager->persist($teacher);
                }
            }

            $city = $form->get('city')->getData();
            if (is_numeric($city)) {
                $cityEntity = $this->cityRepository->findOneBy(['id' => $city]);
                if (!is_null($cityEntity)) {
                    $teacher->setCity($cityEntity);
                    $this->entityManager->persist($teacher);
                }
            }

            $this->entityManager->flush();
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Teacher::class,
        ]);
    }
}
