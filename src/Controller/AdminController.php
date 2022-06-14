<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Teacher;
use App\Form\AdminTeacherEditType;
use App\Form\UserAddType;
use App\Repository\ReviewRepository;
use App\Repository\TeacherRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @var ReviewRepository
     */
    private $reviewRepository;
    /**
     * @var TeacherRepository
     */
    private $teacherRepository;
    /**
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;

    /**
     * @param ReviewRepository $reviewRepository
     * @param TeacherRepository $teacherRepository
     * @param UserPasswordHasherInterface $userPasswordHasher
     */
    public function __construct(
        ReviewRepository            $reviewRepository,
        TeacherRepository           $teacherRepository,
        UserPasswordHasherInterface $userPasswordHasher
    )
    {
        $this->reviewRepository = $reviewRepository;
        $this->teacherRepository = $teacherRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * @Route("/admin-home", name="admin")
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        return $this->render('admin/index.html.twig', [
            'teachersCount' => $this->teacherRepository->getCountOfAll(),
            'teachersPendingCount' => $this->teacherRepository->getCountByStatus('pending'),
            'reviewsCount' => $this->reviewRepository->getCountOfAll(),
            'reviewsPendingCount' => $this->reviewRepository->getCountByStatus('pending')
        ]);
    }

    /**
     * @Route("/admin/teacher/list", name="admin_teacher_list")
     * @return Response
     */
    public function teacherList(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        return $this->render('admin/teacher_list.html.twig', [
            'teachers' => $this->teacherRepository->findBy([], ['createdAt' => 'DESC']),
            'type' => 'всички'
        ]);
    }

    /**
     * @Route("/admin/teacher/list-pending", name="admin_teacher_list_pending")
     * @return Response
     */
    public function listTeachersPending(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        return $this->render('admin/teacher_list.html.twig', [
            'teachers' => $this->teacherRepository->findBy(['status' => 'pending'], ['createdAt' => 'DESC']),
            'type' => 'неодобрени'
        ]);
    }

    /**
     * @Route("/admin/teacher/edit/{id}", name="admin_teacher_edit")
     * @param Teacher $teacher
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editTeacher(
        Teacher                $teacher,
        Request                $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        $form = $this->createForm(AdminTeacherEditType::class, $teacher);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $teacher = $form->getData();

            $entityManager->persist($teacher);
            $entityManager->flush();

            $this->addFlash('success', 'Успешно извършена редакция');

            return $this->redirectToRoute('admin_teacher_edit', ['id' => $teacher->getId()]);
        }

        return $this->render('admin/teacher_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/users/add", name="admin_user_add")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function addUser(
        Request                $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserAddType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_users_list');
        }

        return $this->render('admin/user_add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/users/list", name="admin_users_list")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function listUsers(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/users_list.html.twig', [
            'users' => $userRepository->findBy([], [])
        ]);
    }

    /**
     * @Route("/admin/reviews/list", name="admin_reviews_list")
     * @param ReviewRepository $reviewRepository
     * @return Response
     */
    public function listReviews(ReviewRepository $reviewRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        return $this->render('admin/reviews_list.html.twig', [
            'reviews' => $reviewRepository->findBy([], ['date' => 'DESC']),
            'type' => 'всички'
        ]);
    }

    /**
     * @Route("/admin/reviews/list-pending", name="admin_reviews_list_pending")
     * @param ReviewRepository $reviewRepository
     * @return Response
     */
    public function listReviewsPending(ReviewRepository $reviewRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        return $this->render('admin/reviews_list.html.twig', [
            'reviews' => $reviewRepository->findBy(['status' => 'pending'], ['date' => 'DESC']),
            'type' => 'неодобрени'
        ]);
    }

    /**
     * @Route("/admin/reviews/edit/{id}", name="admin_review_edit")
     * @param Review $review
     * @param TeacherRepository $teacherRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ReviewRepository $reviewRepository
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function editReview(
        Review                 $review,
        TeacherRepository      $teacherRepository,
        Request                $request,
        EntityManagerInterface $entityManager,
        ReviewRepository       $reviewRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        if ($request->getMethod() == 'POST') {
            $review->setTitle($request->request->get('reviewTitle'));
            $review->setContent($request->request->get('reviewContent'));
            $review->setRating($request->request->get('reviewRating'));
            $review->setAuthorName($request->request->get('reviewAuthorName'));
            $review->setStatus($request->request->get('reviewStatus'));

            $teacher = $teacherRepository->findOneBy(['id' => $request->request->get('teacher')]);
            $review->setTeacher($teacher);

            $entityManager->persist($review);

            $entityManager->flush();

            $newTeacherRating = $reviewRepository->getAvgRatingByTeacher((int)$review->getTeacher()->getId());
            $review->getTeacher()->setRating(number_format((float)$newTeacherRating, 2, '.', ''));

            $activeReviewsCount = $reviewRepository->getCountByStatusForTeacher($teacher, 'approved');
            $review->getTeacher()->setActiveReviewsCount($activeReviewsCount);

            $entityManager->persist($review);

            $entityManager->flush();


            return $this->redirectToRoute('admin_reviews_list');
        }

        return $this->render('admin/review_edit.html.twig', [
            'review' => $review,
            'teachers' => $teacherRepository->findAll(),
            'review_statuses' => ['approved', 'inactive', 'pending']
        ]);
    }
}
