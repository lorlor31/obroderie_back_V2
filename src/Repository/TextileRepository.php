<?php

namespace App\Repository;

use App\Entity\Textile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Textile>
 *
 * @method Textile|null find($id, $lockMode = null, $lockVersion = null)
 * @method Textile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Textile[]    findAll()
 * @method Textile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TextileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Textile::class);
    }

    public function findByType($value): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.type = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return Textile[] Returns an array of Textile objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Textile
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
