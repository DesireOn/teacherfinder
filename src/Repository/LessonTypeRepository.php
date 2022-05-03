<?php

namespace App\Repository;

use App\Entity\LessonType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LessonType>
 *
 * @method LessonType|null find($id, $lockMode = null, $lockVersion = null)
 * @method LessonType|null findOneBy(array $criteria, array $orderBy = null)
 * @method LessonType[]    findAll()
 * @method LessonType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LessonType::class);
    }

    /**
     * @param LessonType $entity
     * @param bool $flush
     */
    public function add(LessonType $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param LessonType $entity
     * @param bool $flush
     */
    public function remove(LessonType $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
