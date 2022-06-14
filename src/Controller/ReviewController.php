<?php

namespace App\Controller;


use App\Entity\Review;
use App\Entity\Teacher;
use App\Form\ReviewType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    /**
     * @Route("/add-review-{id}", name="add-review", methods={"GET", "POST"})
     * @param Teacher $teacher
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function addReview(
        Teacher $teacher,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $review = new Review();
        $review->setDate(new DateTimeImmutable('now'));
        $review->setTeacher($teacher);

        $form = $this->createForm(ReviewType::class, $review);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $review = $form->getData();
            $review->setStatus('pending');

            $entityManager->persist($review);

            $entityManager->flush();

            return $this->render('review/review-thank-you.html.twig', []);
        }

        return $this->render('review/add.html.twig', [
            'teacher' => $teacher,
            'form'   => $form->createView(),
        ]);
    }
}
