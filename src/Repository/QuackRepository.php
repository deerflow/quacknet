<?php

namespace App\Repository;

use App\Entity\Quack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Quack|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quack|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quack[]    findAll()
 * @method Quack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quack::class);
    }

    // /**
    //  * @return Quack[] Returns an array of Quack objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Quack
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * @param string $search
     * @return array
     */
    public function findBySearchTerm(string $search): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery("SELECT q FROM App\Entity\Quack q
            WHERE q.author IN (SELECT d FROM App\Entity\Duck d WHERE d.duckname LIKE :search)
            OR q.id IN (SELECT IDENTITY(t.quack_id) FROM App\Entity\Tag t WHERE t.text LIKE :search)")
            ->setParameter('search', '%' . $search . '%');

        return $query->getResult();
    }
}
