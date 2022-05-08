<?php

namespace App\Repository;

use App\Entity\Review;
use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 *
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * @param Review $entity
     * @param bool $flush
     */
    public function add(Review $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Review $entity
     * @param bool $flush
     */
    public function remove(Review $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findReviewsBuilder(Teacher $teacher, array $orderBy, string $reviewStatus = 'approved'): QueryBuilder
    {
        return
            $this->createQueryBuilder('r')
                ->andWhere('r.teacher = :teacher')
                ->setParameter('teacher', $teacher)
                ->andWhere('r.status = :reviewStatus')
                ->setParameter('reviewStatus', $reviewStatus)
                ->orderBy($orderBy['property'], $orderBy['criteria'])
            ;
    }
}
