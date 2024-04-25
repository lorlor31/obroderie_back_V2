<?php

namespace App\Repository;

use App\Entity\Embroidery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\AST\Functions\String\StrReplaceFunction;


/**
 * @extends ServiceEntityRepository<Embroidery>
 *
 * @method Embroidery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Embroidery|null findOneBy(array $criteria, array $orderBy = null)
 * @method Embroidery[]    findAll()
 * @method Embroidery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmbroideryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Embroidery::class);
    }

    public function findByName($value): array
    {
        return $this->createQueryBuilder('e')
        ->andWhere('e.name = :val')
        ->setParameter('val', $value)
        ->orderBy('e.id', 'ASC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult()
    ;
    }
    
    
    
    
    
    public function findByDesign($value): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.design = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return Embroidery[] Returns an array of Embroidery objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Embroidery
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
