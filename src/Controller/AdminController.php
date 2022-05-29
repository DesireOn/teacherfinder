<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Form\AdminTeacherEditType;
use App\Repository\ReviewRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @param ReviewRepository $reviewRepository
     * @param TeacherRepository $teacherRepository
     */
    public function __construct(ReviewRepository $reviewRepository, TeacherRepository $teacherRepository)
    {
        $this->reviewRepository = $reviewRepository;
        $this->teacherRepository = $teacherRepository;
    }

    /**
     * @Route("/admin", name="admin")
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/teacher_list.html.twig', [
            'teachers' => $this->teacherRepository->findBy([], ['createdAt' => 'DESC']),
            'type' => 'всички'
        ]);
    }

    /**
     * @Route("/admin/teacher/list-pending", name="admin_teacher_list_pending")
     * @return Response
     */
    public function listSchoolsPending(): Response
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

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
}
