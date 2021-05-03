<?php

namespace App\Repository;

use App\Entity\Duck;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Duck|null find($id, $lockMode = null, $lockVersion = null)
 * @method Duck|null findOneBy(array $criteria, array $orderBy = null)
 * @method Duck[]    findAll()
 * @method Duck[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DuckRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Duck::class);
    }

    // /**
    //  * @return Duck[] Returns an array of Duck objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Duck
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
