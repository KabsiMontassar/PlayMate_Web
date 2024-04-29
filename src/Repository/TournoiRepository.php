<?php

namespace App\Repository;
use App\Repository\ParticipationRepository;
use App\Entity\Tournoi;
use App\Entity\Participation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @extends ServiceEntityRepository<Tournoi>
 *
 * @method Tournoi|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tournoi|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tournoi[]    findAll()
 * @method Tournoi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TournoiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tournoi::class);
    }

//    /**
//     * @return Tournoi[] Returns an array of Tournoi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tournoi
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


public function countParticipationsForEachTournoi()
{
    // Créer un QueryBuilder avec l'alias 't' pour Tournoi et 'p' pour Participation
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->select('t.id, t.nom, COUNT(p.id) as nombre_participations')
        ->from(Tournoi::class, 't')
        ->leftJoin(Participation::class, 'p', 'WITH', 't.id = p.idtournoi')
        ->groupBy('t.id');

    // Exécuter la requête et obtenir les résultats
    return $qb->getQuery()->getResult();
}

          
}
