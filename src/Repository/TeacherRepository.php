<?php

namespace App\Repository;

use App\Entity\Teacher;
use App\Filter\TeacherFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Teacher>
 *
 * @method Teacher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Teacher|null findOneBy(array $criteria, array $orderBy = null)
 * @method Teacher[]    findAll()
 * @method Teacher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeacherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Teacher::class);
    }

    /**
     * @param Teacher $entity
     * @param bool $flush
     */
    public function add(Teacher $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Teacher $entity
     * @param bool $flush
     */
    public function remove(Teacher $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param array $orderBy
     * @param TeacherFilter $filter
     * @return QueryBuilder
     */
    public function findTeachersByFilterBuilder(array $orderBy, TeacherFilter $filter): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->andWhere('t.status = :teacherStatus')
            ->setParameter('teacherStatus', 'approved')
            ->orderBy($orderBy['property'], $orderBy['criteria'])
        ;

        if (!is_null($filter->getLessonType())) {
            $queryBuilder->innerJoin(
                't.lessonTypes', 'lt', Join::WITH, 'lt.type = :lessonType'
            );
            $queryBuilder->setParameter('lessonType', $filter->getLessonType());
        }

        if (!is_null($filter->getCity())) {
            $queryBuilder->andWhere('t.city = :city');
            $queryBuilder->setParameter('city', $filter->getCity());
        }

        if (!is_null($filter->getMinPrice())) {
            $queryBuilder->andWhere('t.pricePerHour >= :minPrice');
            $queryBuilder->setParameter('minPrice', $filter->getMinPrice());
        }

        if (!is_null($filter->getMaxPrice())) {
            $queryBuilder->andWhere('t.pricePerHour <= :maxPrice');
            $queryBuilder->setParameter('maxPrice', $filter->getMaxPrice());
        }

        if (!is_null($filter->getGender()) && $filter->getGender() !== 'all') {
            $queryBuilder->andWhere('t.gender = :gender');
            $queryBuilder->setParameter('gender', $filter->getGender());
        }

        if (!is_null($filter->getSubject())) {
            $queryBuilder->andWhere('t.subject = :subject');
            $queryBuilder->setParameter('subject', $filter->getSubject());
        }

        return $queryBuilder;

    }

    /**
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCountOfAll()
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }


    public function getCountByStatus(string $status)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.status = :status')
            ->setParameter('status', $status)
            ->select('count(s.id)')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
}
