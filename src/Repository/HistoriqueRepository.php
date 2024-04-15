<?php

namespace App\Repository;

use App\Entity\Historique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Historique>
 *
 * @method Historique|null find($id, $lockMode = null, $lockVersion = null)
 * @method Historique|null findOneBy(array $criteria, array $orderBy = null)
 * @method Historique[]    findAll()
 * @method Historique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoriqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Historique::class);
    }

    //    /**
    //     * @return Historique[] Returns an array of Historique objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Historique
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * 
     *
     * @param int $idMembre L'ID du membre
     * @return Historique[] Un tableau d'objets Historique
     */
    public function findHistoriqueByMembreId(int $idMembre): array
    {
        return $this->createQueryBuilder('h')
            ->join('h.reservation', 'r')
            ->join('r.payment', 'p')
            ->where('p.idmembre = :idMembre')
            ->setParameter('idMembre', $idMembre)
            ->getQuery()
            ->getResult();
    }
}
