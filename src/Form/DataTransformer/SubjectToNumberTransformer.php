<?php

namespace App\Form\DataTransformer;

use App\Entity\Subject;
use App\Entity\Teacher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SubjectToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (subject) to a string (number).
     *
     * @param  Teacher|null $value
     */
    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        return $value->getId();
    }

    /**
     * Transforms a string (teacherId) to an object (teacher).
     * @param string $value
     * @return Subject|null
     */
    public function reverseTransform($value): ?Subject
    {
        if (!$value) {
            return null;
        }

        $subject = $this->entityManager
            ->getRepository(Subject::class)
            ->find($value)
        ;

        if (null === $subject) {
            throw new TransformationFailedException(sprintf(
                'A subject with number %s does not exist!',
                $value
            ));
        }

        return $subject;
    }
}