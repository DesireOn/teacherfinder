<?php

namespace App\Filter;


use App\Entity\City;
use App\Entity\Subject;
use App\Repository\CityRepository;
use App\Repository\SubjectRepository;

class TeacherFilter
{
    private $lessonType;

    private $city;

    private $minPrice;

    private $maxPrice;

    private $gender;

    private $subject;

    private $subjectRepository;

    private $cityRepository;

    /**
     * @param CityRepository $cityRepository
     * @param SubjectRepository $subjectRepository
     */
    public function __construct(
        CityRepository $cityRepository,
        SubjectRepository $subjectRepository
    )
    {
        $this->cityRepository = $cityRepository;
        $this->subjectRepository = $subjectRepository;
    }

    /**
     * @param array $filters
     * @return void
     */
    public function fromArray(array $filters): TeacherFilter
    {
        $lessonTypes = ['online', 'in-person'];
        if (isset($filters['lessonType']) && in_array($filters['lessonType'], $lessonTypes)) {
            $this->setLessonType($filters['lessonType']);
        } else {
            $this->setLessonType('online');
        }

        if (isset($filters['cityId']) && is_numeric($filters['cityId'])) {
            $city = $this->cityRepository->findOneBy(['id' => $filters['cityId']]);
            if (!is_null($city)) {
                $this->setCity($city);
            }
        }

        if (isset($filters['minPrice']) && is_numeric($filters['minPrice'])) {
            $this->setMinPrice($filters['minPrice']);
        }

        if (isset($filters['maxPrice']) && is_numeric($filters['maxPrice'])) {
            $this->setMaxPrice($filters['maxPrice']);
        }

        $genders = ['m', 'f', 'all'];
        if (isset($filters['gender']) && in_array($filters['gender'], $genders)) {
            $this->setGender($filters['gender']);
        }

        if (isset($filters['subjectId'])) {
            $subject = $this->subjectRepository->findOneBy(['id' => $filters['subjectId']]);
            if (!is_null($subject)) {
                $this->setSubject($subject);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLessonType(): ?string
    {
        return $this->lessonType;
    }

    /**
     * @param mixed $lessonType
     * @return TeacherFilter
     */
    public function setLessonType($lessonType): TeacherFilter
    {
        $this->lessonType = $lessonType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     * @return TeacherFilter
     */
    public function setCity($city): TeacherFilter
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinPrice(): ?string
    {
        return $this->minPrice;
    }

    /**
     * @param mixed $minPrice
     * @return TeacherFilter
     */
    public function setMinPrice($minPrice): TeacherFilter
    {
        $this->minPrice = $minPrice;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxPrice(): ?string
    {
        return $this->maxPrice;
    }

    /**
     * @param mixed $maxPrice
     * @return TeacherFilter
     */
    public function setMaxPrice($maxPrice): TeacherFilter
    {
        $this->maxPrice = $maxPrice;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     * @return TeacherFilter
     */
    public function setGender($gender): TeacherFilter
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     * @return TeacherFilter
     */
    public function setSubject($subject): TeacherFilter
    {
        $this->subject = $subject;

        return $this;
    }
}