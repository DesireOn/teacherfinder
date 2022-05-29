<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Filter\TeacherFilter;
use App\Form\TeacherSubmitType;
use App\Repository\CityRepository;
use App\Repository\ReviewRepository;
use App\Repository\SubjectRepository;
use App\Repository\TeacherRepository;
use App\Sorting\ReviewSorting;
use App\Sorting\TeacherSorting;
use App\Uploader\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @Route("/", name="teacher_home")
     */
    public function home(
        SubjectRepository $subjectRepository,
        CityRepository $cityRepository
    ): Response
    {
        return $this->render('teacher/home.html.twig', [
            'subjects' => $subjectRepository->findAll(),
            'cities' => $cityRepository->findAll()
        ]);
    }

    /**
     * @Route("/teacher/list", name="teacher_list")
     */
    public function list(
        Request $request,
        TeacherFilter $teacherFilter,
        SubjectRepository $subjectRepository,
        CityRepository $cityRepository,
        TeacherSorting $teacherSorting
    ): Response
    {
        $requestOrderBy = $request->get('orderBy') ?: 'highest';
        $orderByCriteria = $teacherSorting->sort($requestOrderBy);

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

    /**
     * @Route("/teacher/{teacher}", name="teacher_show")
     */
    public function show(
        TeacherRepository $teacherRepository,
        ReviewSorting $reviewSorting,
        Request $request,
        ReviewRepository $reviewRepository
    ): Response
    {
        $teacher = $teacherRepository->findOneBy(['id' => $request->get('teacher'), 'status' => 'approved']);

        if (is_null($teacher)) {
            throw new NotFoundHttpException('Teacher was not found');
        }

        $orderBy = $reviewSorting->sort($request->get('orderBy') ?: 'newest');

        $qb = $reviewRepository->findReviewsBuilder($teacher, $orderBy);
        $adapter = new QueryAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(5);
        $pagerfanta->setCurrentPage($request->get('page', 1));

        $reviews = [];
        foreach ($pagerfanta->getCurrentPageResults() as $currentPageResult) {
            $reviews[] = $currentPageResult;
        }

        return $this->render('teacher/show.html.twig', [
            'teacher' => $teacher,
            'pagerfanta' => $pagerfanta,
            'reviews' => $reviews,
            'orderBy' => $request->get('orderBy') ?: 'newest',
        ]);
    }

    /**
     * @Route("/teacher-submit", name="teacher_submit")
     */
    public function submit(
        Request $request,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader
    ): Response
    {
        $form = $this->createForm(TeacherSubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $teacher = $form->getData();

            if ($teacher instanceof Teacher) {
                $logoFile = $form->get('logo')->getData();
                if ($logoFile) {
                    $teacherLogoName = $fileUploader->upload($logoFile);
                    $teacher->setLogo($teacherLogoName);
                }

                $entityManager->persist($teacher);
                $entityManager->flush();
            }

            return $this->redirectToRoute('teacher_submit_thank_you');
        }

        return $this->render('teacher/submit.html.twig', [
            'form' => $form->createView(),
            'hideForm' => $request->get('hideForm') ?? false
        ]);
    }

    /**
     * @Route("/thank-you", name="teacher_submit_thank_you")
     */
    public function thankYou(): Response
    {
        return $this->render('teacher/submit-thank-you.html.twig');
    }
}
