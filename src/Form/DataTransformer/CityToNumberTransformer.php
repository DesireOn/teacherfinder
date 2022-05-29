<?php

namespace App\Form\DataTransformer;

use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CityToNumberTransformer implements DataTransformerInterface
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
     * Transforms an object (city) to a string (number).
     *
     * @param City|null $value
     */
    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        return $value->getId();
    }

    /**
     * Transforms a string (cityId) to an object (city).
     * @param string $value
     * @return City|null
     */
    public function reverseTransform($value): ?City
    {
        if (!$value) {
            return null;
        }

        $city = $this->entityManager
            ->getRepository(City::class)
            ->find($value)
        ;

        if (null === $city) {
            throw new TransformationFailedException(sprintf(
                'A city with number %s does not exist!',
                $value
            ));
        }

        return $city;
    }
}