<?php

namespace App\Repository;

use App\Entity\CustOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CustOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustOrder[]    findAll()
 * @method CustOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustOrderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CustOrder::class);
    }

    // /**
    //  * @return CustOrder[] Returns an array of CustOrder objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CustOrder
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
