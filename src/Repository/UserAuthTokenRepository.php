<?php

namespace App\Repository;

use App\Entity\UserAuthToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAuthStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAuthStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAuthStatus[]    findAll()
 * @method UserAuthStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAuthTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAuthToken::class);
    }

    // /**
    //  * @return UserAuthStatus[] Returns an array of UserAuthStatus objects
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
    public function findOneBySomeField($value): ?UserAuthStatus
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
