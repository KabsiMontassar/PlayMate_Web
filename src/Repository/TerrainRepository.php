<?php

namespace App\Repository;

use App\Entity\Terrain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Terrain>
 *
 * @method Terrain|null find($id, $lockMode = null, $lockVersion = null)
 * @method Terrain|null findOneBy(array $criteria, array $orderBy = null)
 * @method Terrain[]    findAll()
 * @method Terrain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */ 
class TerrainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Terrain::class);
    } 
    public function findByAddress($address)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.address LIKE :address')
            ->setParameter('address', '%'.$address.'%')
            ->getQuery()
            ->getResult();
    }
    public function findByGouvernorat($gouvernorat)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.gouvernorat LIKE :gouvernorat')
            ->setParameter('gouvernorat', '%'.$gouvernorat.'%')
            ->getQuery()
            ->getResult();
    }
    public function findAllOrderByPrice($order = 'ASC')
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.prix', $order)
            ->getQuery()
            ->getResult();
    }

    public function findAllOrderByDuration($order = 'ASC')
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.duree', $order)
            ->getQuery()
            ->getResult();
    }
    public function findByAddressOrGouvernorat($query)
{
    return $this->createQueryBuilder('t')
        ->andWhere('t.address LIKE :query OR t.gouvernorat LIKE :query')
        ->setParameter('query', '%'.$query.'%')
        ->getQuery()
        ->getResult();
}

}
