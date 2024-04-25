<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
        // filter all the products using embroidery $embroideryId, DESC order
        public function productsUsingEmbroidery($embroideryId): array
        {

            return $this->createQueryBuilder('p')
                ->select ("p.id,p.quantity")
                ->join('p.embroidery', 'embroidery')
                ->andWhere('embroidery.id = :embroideryId')
                ->setParameter('embroideryId', $embroideryId)
                ->orderBy('p.quantity', 'DESC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult()
            ;
        }

        public function sumOfquantitiesOfProductsUsingEmbroidery($embroideryId): array
        {

            return $this->createQueryBuilder('p')
                ->select("SUM(p.quantity) AS sum")
                ->join('p.embroidery', 'embroidery')
                ->andWhere('embroidery.id = :embroideryId')
                ->setParameter('embroideryId', $embroideryId)
                // ->orderBy('embroidery.id', 'ASC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult()
            ;
        }
}
