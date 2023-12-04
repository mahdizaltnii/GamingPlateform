<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    /**
     * recupere les annonces en lien avec recherche
     * @return Annonce[]
     */
    public function findSearch(SearchData $search):array
    {
        $query= $this
            ->createQueryBuilder('p')
            ->select('c','p')
            ->join('p.categorie','c');
        if (!empty($search->q))
        {
            $query=$query
                ->andWhere('p.title LIKE :q OR p.prix LIKE :q')
                ->setParameter('q',"%{$search->q}%");
        }
        if (!empty($search->categorie))
        {
            $query=$query
                ->andWhere('c.id IN (:categorie)')
                ->setParameter('categorie',$search->categorie);
        }
        if (!empty($search->min)){
            $query = $query
                ->andWhere('p.prix >= :min')
                ->setParameter('min', $search->min);
        }
        if (!empty($search->max)){
            $query = $query
                ->andWhere('p.prix <= :max')
                ->setParameter('max', $search->max);
        }
        return $query->getQuery()->getResult();
    }
}
