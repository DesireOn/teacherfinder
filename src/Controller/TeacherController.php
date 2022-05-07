<?php

namespace App\Controller;

use App\Filter\TeacherFilter;
use App\Repository\CityRepository;
use App\Repository\SubjectRepository;
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
    public function list(
        Request $request,
        TeacherFilter $teacherFilter,
        SubjectRepository $subjectRepository,
        CityRepository $cityRepository
    ): Response
    {
        $requestOrderBy = $request->get('orderBy') ?: 'highest';
        $orderByCriteria = $this->sortTeachers($requestOrderBy);

        $filter = $teacherFilter->fromArray($request->query->all());

        $qb = $this->teacherRepository->findTeachersByFilterBuilder($orderByCriteria, $filter);
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
            'subjects' => $subjectRepository->findAll(),
            'cities' => $cityRepository->findAll()
        ]);
    }

    public function sortTeachers(string $filter): array
    {
        if ($filter === 'highest') {
            return [
                'property' => 't.rating',
                'criteria' => 'DESC'
            ];
        } elseif ($filter === 'lowest') {
            return [
                'property' => 't.rating',
                'criteria' => 'ASC'
            ];
        } elseif ($filter === 'cheapest') {
            return [
                'property' => 't.pricePerHour',
                'criteria' => 'ASC'
            ];
        } else {
            return [
                'property' => 't.pricePerHour',
                'criteria' => 'DESC'
            ];
        }
    }
}
