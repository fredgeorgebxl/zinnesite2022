<?php

namespace App\Repository;

use App\Entity\HomeSlide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HomeSlide|null find($id, $lockMode = null, $lockVersion = null)
 * @method HomeSlide|null findOneBy(array $criteria, array $orderBy = null)
 * @method HomeSlide[]    findAll()
 * @method HomeSlide[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HomeSlideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HomeSlide::class);
    }

    // /**
    //  * @return HomeSlide[] Returns an array of HomeSlide objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HomeSlide
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
