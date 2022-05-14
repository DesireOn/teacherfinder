<?php

namespace App\DataFixtures;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use App\Repository\TeacherRepository;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var TeacherRepository
     */
    private $teacherRepository;
    /**
     * @var ReviewRepository
     */
    private $reviewRepository;

    /**
     * @param TeacherRepository $teacherRepository
     * @param ReviewRepository $reviewRepository
     */
    public function __construct(TeacherRepository $teacherRepository, ReviewRepository $reviewRepository)
    {
        $this->teacherRepository = $teacherRepository;
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $reviews = [
            [
                'title' => 'Добър учител',
                'name' => 'Иван Иванов',
                'rating' => 3
            ],
            [
                'title' => 'Останах много доволен от качествените уроци!',
                'name' => 'Борис Петров',
                'rating' => 5
            ],
            [
                'title' => 'Сравнително добре, но не бих го избрал пак',
                'name' => 'Владимир Илиев',
                'rating' => 2
            ],
            [
                'title' => 'Свърши отлична работа. Помогна ми за изпитите',
                'name' => 'Владимир Илиев',
                'rating' => 4
            ],
            [
                'title' => 'Не съм доволен!',
                'name' => 'Стоил Палев',
                'rating' => 1
            ],
            [
                'title' => 'Отлично отношение',
                'name' => 'Богомил Стоянов',
                'rating' => 5
            ],
            [
                'title' => 'Учителят си върши чудесно работата',
                'name' => 'Йордан Траянов',
                'rating' => 5
            ],
            [
                'title' => 'Очарован съм от вниманието',
                'name' => 'Чудомир Василев',
                'rating' => 5
            ],
            [
                'title' => 'Не съм особено впечатлен',
                'name' => 'Васил Грешков',
                'rating' => 2
            ],
            [
                'title' => 'Помогна ми да си взема изпита успешно!',
                'name' => 'Васил Грешков',
                'rating' => 4
            ]
        ];

        $content = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";

        $teachers = $this->teacherRepository->findAll();

        foreach ($teachers as $teacher) {
            $ratingSum = 0;
            $numberOfReviews = random_int(1,5);

            $usedReviews = [];

            for ($i = 0; $i < $numberOfReviews; $i++) {
                $randomReview = $reviews[array_rand($reviews)];
                if (!in_array($randomReview['title'], $usedReviews)) {
                    $usedReviews[] = $randomReview['title'];

                    $review = new Review();
                    $review->setTeacher($teacher);
                    $review->setTitle($randomReview['title']);
                    $review->setRating($randomReview['rating']);
                    $review->setAuthorName($randomReview['name']);
                    $review->setContent($content);
                    $review->setDate(new DateTimeImmutable('now'));
                    $review->setHideAuthorName(0);
                    $review->setStatus('approved');

                    $manager->persist($review);

                    $ratingSum += $review->getRating();
                }
            }

            $teacherRating = $ratingSum / 5;
            $teacher->setRating($teacherRating);
            $teacher->setActiveReviewsCount(count($usedReviews));

            $manager->persist($teacher);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [TeacherFixtures::class];
    }
}
