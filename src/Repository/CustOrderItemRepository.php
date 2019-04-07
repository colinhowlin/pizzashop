<?php

namespace App\Repository;

use App\Entity\CustOrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CustOrderItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustOrderItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustOrderItem[]    findAll()
 * @method CustOrderItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustOrderItemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CustOrderItem::class);
    }

    // /**
    //  * @return CustOrderItem[] Returns an array of CustOrderItem objects
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
    public function findOneBySomeField($value): ?CustOrderItem
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
