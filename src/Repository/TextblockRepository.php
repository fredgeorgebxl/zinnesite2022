<?php

namespace App\Repository;

use App\Entity\Textblock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Textblock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Textblock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Textblock[]    findAll()
 * @method Textblock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TextblockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Textblock::class);
    }

    // /**
    //  * @return Textblock[] Returns an array of Textblock objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Textblock
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
