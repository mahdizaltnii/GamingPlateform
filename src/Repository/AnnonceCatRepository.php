<?php

namespace App\Repository;

use App\Entity\AnnonceCat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnnonceCat|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnnonceCat|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnnonceCat[]    findAll()
 * @method AnnonceCat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceCatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnnonceCat::class);
    }

    // /**
    //  * @return AnnonceCatController[] Returns an array of AnnonceCatController objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AnnonceCatController
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
