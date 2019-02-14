<?php

namespace App\Repository;

use App\Entity\OrderedArticles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OrderedArticles|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderedArticles|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderedArticles[]    findAll()
 * @method OrderedArticles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderedArticlesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OrderedArticles::class);
    }

    // /**
    //  * @return OrderedArticles[] Returns an array of OrderedArticles objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderedArticles
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
