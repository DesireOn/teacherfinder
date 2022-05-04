<?php

namespace App\Controller;

use App\Repository\TeacherRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeacherController extends AbstractController
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

    /**
     * @Route("/teacher/list", name="teacher_list")
     */
    public function list(Request $request): Response
    {
        $requestOrderBy = $request->get('orderBy') ?: 'highest';
        $orderBy = $this->sortTeachers($requestOrderBy);

        $qb = $this->teacherRepository->findTeachersByStatusBuilder('approved', $orderBy);
        $adapter = new QueryAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(5);
        $pagerfanta->setCurrentPage($request->get('page', 1));

        $teachers = [];
        foreach ($pagerfanta->getCurrentPageResults() as $currentPageResult) {
            $teachers[] = $currentPageResult;
        }

        return $this->render('teacher/list.html.twig', [
            'teachers' => $teachers,
            'pagerfanta' => $pagerfanta,
            'orderBy' => $requestOrderBy,
        ]);
    }

    public function sortTeachers(string $filter): array
    {
        return [
            'property' => 't.rating',
            'criteria' => 'DESC'
        ];
    }
}
