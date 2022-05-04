<?php

namespace App\Repository;

use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
     * @param string $teacherStatus
     * @param array $orderBy
     * @return QueryBuilder
     */
    public function findTeachersByStatusBuilder(string $teacherStatus, array $orderBy): QueryBuilder
    {
        return
            $this->createQueryBuilder('t')
                ->andWhere('t.status = :teacherStatus')
                ->setParameter('teacherStatus', $teacherStatus)
                ->orderBy($orderBy['property'], $orderBy['criteria'])
            ;
    }
}
