<?php

namespace App\Controller;

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
        ReviewRepository $reviewRepository,
        TeacherRepository $teacherRepository,
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
        Teacher $teacher,
        Request $request,
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
        Request $request,
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
}
