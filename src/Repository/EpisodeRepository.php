<?php

namespace App\Repository;

use App\Entity\Episode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Episode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Episode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Episode[]    findAll()
 * @method Episode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpisodeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Episode::class);
    }

    public function findAllSorted()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.numberForSort', 'ASC')
            ->setMaxResults(1000)
            ->getQuery()
            ->getResult()
            ;

    }

    public function findAllPublishedSorted()
    {
        return $this->createQueryBuilder('e')
            ->where('e.published <= CURRENT_TIMESTAMP()')
            ->orderBy('e.numberForSort', 'ASC')
            ->setMaxResults(1000)
            ->getQuery()
            ->getResult()
            ;

    }

    public function findOneByGuid($guid): ?Episode
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.guid = :guid')
            ->setParameter('guid', $guid)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return Episode[] Returns an array of Episode objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Episode
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
