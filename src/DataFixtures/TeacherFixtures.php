<?php

namespace App\DataFixtures;

use App\Entity\Teacher;
use App\Repository\CityRepository;
use App\Repository\SubjectRepository;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TeacherFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var SubjectRepository
     */
    private $subjectRepository;
    /**
     * @var CityRepository
     */
    private $cityRepository;

    /**
     * @param SubjectRepository $subjectRepository
     * @param CityRepository $cityRepository
     */
    public function __construct(SubjectRepository $subjectRepository, CityRepository $cityRepository)
    {
        $this->subjectRepository = $subjectRepository;
        $this->cityRepository = $cityRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $teachers = [
            [
                'name' => 'Виктория Иванова',
                'logo' => 'viktoria.jpg',
                'email' => 'viktoria@gmail.com',
                'phone' => '0883945849',
                'gender' => 'f'
            ],
            [
                'name' => 'Мария Димитров',
                'logo' => 'maria.jpg',
                'email' => 'maria@gmail.com',
                'phone' => '0895029342',
                'gender' => 'f'
            ],
            [
                'name' => 'Мартин Петров',
                'logo' => 'martin.jpg',
                'email' => 'martin@gmail.com',
                'phone' => '0884039285',
                'gender' => 'm'
            ],
            [
                'name' => 'Димитър Георгиев',
                'logo' => 'dimitar.jpg',
                'email' => 'dimitar@gmail.com',
                'phone' => '0894859384',
                'gender' => 'm'
            ],
            [
                'name' => 'Никола Василев',
                'logo' => 'nikola.jpg',
                'email' => 'nikola@gmail.com',
                'phone' => '0894028394',
                'gender' => 'm'
            ],
            [
                'name' => 'Пламена Иванова',
                'logo' => 'plamena.jpg',
                'email' => 'plamena@gmail.com',
                'phone' => '0849283947',
                'gender' => 'f'
            ],
            [
                'name' => 'Атанас Стоянов',
                'logo' => 'atanas.jpg',
                'email' => 'atanas@gmail.com',
                'phone' => '0884938475',
                'gender' => 'm'
            ],
            [
                'name' => 'Борислав Методиев',
                'logo' => 'borislav.jpg',
                'email' => 'borislav@gmail.com',
                'phone' => '0884958675',
                'gender' => 'm'
            ],
            [
                'name' => 'Владислав Петров',
                'logo' => 'vladislav.jpg',
                'email' => 'vladislav@gmail.com',
                'phone' => '0884938574',
                'gender' => 'm'
            ],
            [
                'name' => 'Веселина Георгиева',
                'logo' => 'veselina.jpg',
                'email' => 'veselina@gmail.com',
                'phone' => '0885630183',
                'gender' => 'f'
            ],
            [
                'name' => 'Йордан Борисов',
                'logo' => 'yordan.jpg',
                'email' => 'yordan@gmail.com',
                'phone' => '0895048695',
                'gender' => 'm'
            ],
            [
                'name' => 'Красимир Стоянов',
                'logo' => 'krasimir.jpg',
                'email' => 'krasimir@gmail.com',
                'phone' => '0894039567',
                'gender' => 'm'
            ],
            [
                'name' => 'Галина Анастасова',
                'logo' => 'galina.jpg',
                'email' => 'galina@gmail.com',
                'phone' => '0895847592',
                'gender' => 'f'
            ],
            [
                'name' => 'Красимира Маринова',
                'logo' => 'krasimira.jpg',
                'email' => 'krasimira@gmail.com',
                'phone' => '0849568401',
                'gender' => 'f'
            ],
            [
                'name' => 'Васил Маринов',
                'logo' => 'vasil.jpg',
                'email' => 'vasil@gmail.com',
                'phone' => '0884956943',
                'gender' => 'm'
            ]
        ];

        $description = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";

        foreach ($teachers as $teacher) {
            $subjects = $this->subjectRepository->findAll();
            $cities = $this->cityRepository->findAll();

            $randomSubject = $subjects[array_rand($subjects)];
            $randomCity = $cities[array_rand($cities)];

            $teacherEntity = new Teacher();
            $teacherEntity->setName($teacher['name']);
            $teacherEntity->setCreatedAt(new DateTimeImmutable('now'));
            $teacherEntity->setRating(1);
            $teacherEntity->setEmail($teacher['email']);
            $teacherEntity->setPhone($teacher['phone']);
            $teacherEntity->setLogo($teacher['logo']);
            $teacherEntity->setStatus('approved');
            $teacherEntity->setDescription($description);
            $teacherEntity->setActiveReviewsCount(0);
            $teacherEntity->setPricePerHour(mt_rand (300, 1000) / 10);
            $teacherEntity->setGender($teacher['gender']);
            $teacherEntity->setSubject($randomSubject);
            $teacherEntity->setCity($randomCity);

            $manager->persist($teacherEntity);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            CityFixtures::class,
            SubjectFixtures::class
        ];
    }
}
