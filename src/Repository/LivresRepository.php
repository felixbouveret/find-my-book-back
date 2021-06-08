<?php

namespace App\Repository;

use App\Entity\Livres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Livres|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livres|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livres[]    findAll()
 * @method Livres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livres::class);
    }

    public function getBestRatedBooks($limit) {
        $books = $this->findAll();

        $allBooksGrades = array();
        foreach ($books as $key => $value) {
            $isNote = count($value->getNotes()->getValues());
            if($isNote === 0) {
                continue;
            }
            $allBooksGrades[$key] = $value->getNotes()->getValues();
        }

        $allBooksAverage = array();

        foreach ($allBooksGrades as $key => $value) {
            $average = 0;
            foreach ($allBooksGrades[$key] as $notes) {
                $average += $notes->getValue();
            }
            if (sizeof($allBooksGrades[$key]) === 0) {
                continue;
            } else {
                $allBooksAverage[$key] = ["book" => $value[0]->getLivre(), "average" => round($average / sizeof($allBooksGrades[$key], 2), 2)];
            }
        }
        
        usort($allBooksAverage, function($a, $b) {
            return $b['average'] <=> $a['average'];
        });
        
        $result = array_slice($allBooksAverage, 0, intval($limit), true);

        return $result;
    
    }
    
    public function findByMultipleId($arrayId)
    {
        $query = $this->createQueryBuilder('l');
        foreach($arrayId as $id) {
            $query = $query->orWhere("l.categorie = $id");
        }

        return $query->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
        
    }

    // /**
    //  * @return Livres[] Returns an array of Livres objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Livres
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}