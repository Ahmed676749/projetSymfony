<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;


/**
 * @extends ServiceEntityRepository<Annonce>
 *
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

    public function save(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
    * @return Annonce[]
    */
    public function findAllNotSold(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isSold = false')
            ->getQuery() // permet de créer un objet utilisable pour récupérer le résultat
            ->getResult() // permet de récupérer le résultat
        ;
    }

    /**
     * @return Annonce[]
     */
    public function findLatestNotSold(): array
    {
        return $this->findNotSoldQuery()
            ->setMaxResults(3)
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return QueryBuilder
     */
    private function findNotSoldQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isSold = false')
        ;
    }

    public function findAllNotSoldQuery(): Query // knp_paginator aura besoin d'un objet de type Query pour fonctionner, c'est pour cela que nous avons besoin de cette fonction
    {
        return $this->findNotSoldQuery()
            ->getQuery()
        ;
    }

//    /**
//     * @return Annonce[] Returns an array of Annonce objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Annonce
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
