<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            'schools' => $this->teacherRepository->findBy([], ['createdAt' => 'DESC']),
            'type' => 'всички'
        ]);
    }
}
