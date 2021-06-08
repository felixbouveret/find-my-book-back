<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    // /**
    //  * @return Categorie[] Returns an array of Categorie objects
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

    public function findByMultipleId($arrayId)
    {
        $query = $this->createQueryBuilder('c');
        foreach($arrayId as $id) {
            $query = $query->orWhere("c.id = $id");
        }

        return $query->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
        

    }

    public function findAllLimited($value): Array
    {
        return $this->createQueryBuilder('c')
            ->setMaxResults($value)
            ->getQuery()
            ->getResult();
    }
    

    public function searchCat($value) : ?Categorie {
        return $this->createQueryBuilder('c')
            ->andWhere('c.label = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}