<?php

namespace App\Repository;

use App\Entity\UserShippingData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserShippingData|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserShippingData|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserShippingData[]    findAll()
 * @method UserShippingData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserShippingDataRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserShippingData::class);
    }

    // /**
    //  * @return UserShippingData[] Returns an array of UserShippingData objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserShippingData
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
