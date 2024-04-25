<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 *
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    //    /**
    //     * @return Customer[] Returns an array of Customer objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
    public function findByType($value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findCustomerByName($customerName): array
          {
              return $this->createQueryBuilder('customer')
                ->andWhere('customer.name LIKE :customerName')
                  ->setParameter('customerName', '%' . $customerName . '%')
                  ->orderBy('customer.id', 'ASC')
                  ->setMaxResults(10)
                  ->getQuery()
                  ->getResult();
          }

    public function findCustomerEmail($customerEmail): array
          {
              return $this->createQueryBuilder('customer')
                ->andWhere('customer.email LIKE :customerEmail')
                  ->setParameter('customerEmail', '%' . $customerEmail . '%')
                  ->orderBy('customer.id', 'ASC')
                  ->setMaxResults(10)
                  ->getQuery()
                  ->getResult();
          }

    public function countCustomersByEmail(string $email): int
          {
              return $this->createQueryBuilder('customer')
                  ->select('COUNT(customer.id)')
                  ->andWhere('customer.email = :email')
                  ->setParameter('email', $email)
                  ->getQuery()
                  ->getSingleScalarResult();
          }

    public function findByPhoneNumber(string $phoneNumber): array
          {
              return $this->createQueryBuilder('customer')
                  ->andWhere('customer.phoneNumber LIKE :phone_number')
                  ->setParameter('phone_number', $phoneNumber.'%' )
                  ->getQuery()
                  ->getResult();
          }
    //    public function findOneBySomeField($value): ?Customer
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
