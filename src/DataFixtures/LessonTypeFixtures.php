<?php

namespace App\DataFixtures;

use App\Entity\LessonType;
use App\Repository\TeacherRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LessonTypeFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var TeacherRepository
     */
    private $teacherRepository;

    /**
     * @param TeacherRepository $teacherRepository
     */
    public function __construct(TeacherRepository $teacherRepository)
    {
        $this->teacherRepository = $teacherRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $lessonTypes = ['online', 'in-person'];
        $teachers = $this->teacherRepository->findAll();

        foreach ($teachers as $teacher) {
            if ($teacher->getRating() >= 3) {
                $lessonType = new LessonType();
                $lessonType->setTeacher($teacher);
                $lessonType->setType('online');
                $manager->persist($lessonType);

                $lessonType = new LessonType();
                $lessonType->setTeacher($teacher);
                $lessonType->setType('in-person');
                $manager->persist($lessonType);
            } else {
                $lessonType = new LessonType();
                $lessonType->setTeacher($teacher);
                $lessonType->setType($lessonTypes[array_rand($lessonTypes)]);
                $manager->persist($lessonType);
            }
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            TeacherFixtures::class
        ];
    }
}
