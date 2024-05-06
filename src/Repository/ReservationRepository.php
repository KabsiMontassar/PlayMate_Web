<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Terrain;
use App\Repository\TerrainRepository;
use DateTime;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    //    /**
    //     * @return Reservation[] Returns an array of Reservation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reservation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findPreviousReservations(\DateTimeInterface $currentDate): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.datereservation  < :currentDate')
            ->setParameter('currentDate', $currentDate)
            ->getQuery()
            ->getResult();
    }


    public function findByDisponibility($idTerrain, $heure, $date, $entityManager): bool
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.idterrain = :id')
            ->andWhere('r.datereservation = :date')
            ->andWhere('r.type NOT IN (:reservationTypes)')
            ->setParameter('id', $idTerrain)
            ->setParameter('reservationTypes', ['Compte_desactive', 'Annulation'])
            ->setParameter('date', $date)
            ->getQuery();

        $reservations = $qb->getResult();

        if (empty($reservations)) {
            return true;
        } else {
            foreach ($reservations as $reservation) {
                $reservationDate = $reservation->getDatereservation()->format('Y-m-d');
                if ($reservationDate == $date) {
                    $heureMatchReserve = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $reservation->getHeureReservation());
                    $heureNouveauMatch = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $heure);

                    $duree = $entityManager->getRepository(Terrain::class)->findOneBy(['id' => $idTerrain])->getDuree();
                    $seCroisent = $this->verifCroisementHoraire($heureMatchReserve, $duree, $heureNouveauMatch);
                    if ($seCroisent) {
                        return false;
                    }
                }
            }
            return true;
        }
    }

    private function verifCroisementHoraire(DateTime $heureMatchReserve, int $dureeAnnonce, DateTime $heureNouveauMatch): bool
    {
        // Cloner les objets DateTime pour éviter de modifier les objets originaux
        $finReserve = clone $heureMatchReserve;
        $finNouveau = clone $heureNouveauMatch;

        // Appliquer les modifications pour obtenir l'heure de fin
        $finReserve = $finReserve->modify("+ $dureeAnnonce minutes");
        $finNouveau = $finNouveau->modify("+ $dureeAnnonce minutes");

        // Comparer les intervalles de temps pour déterminer s'ils se chevauchent
        return !($finReserve < $heureNouveauMatch || $finNouveau < $heureMatchReserve);
    }


    public function findFutureAndUniqueReservations(): array
    {

        $currentDate = new \DateTime();

        return $this->createQueryBuilder('r')
            ->where('r.datereservation > :currentDate')
            ->andWhere('r.type NOT IN (:reservationTypes)')
            ->setParameter('currentDate', $currentDate)
            ->setParameter('reservationTypes', ['Lancez_Vous', 'Compte_desactive', 'Annulation'])
            ->groupBy('r.datereservation, r.heurereservation, r.idterrain')
            ->having('COUNT(r) = 1')
            ->orderBy('r.datereservation', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findFutureReservationsForMember(int $idmembre): array
    {
        return $this->createQueryBuilder('r')
            ->join('App\Entity\Payment', 'p', 'WITH', 'p.idreservation = r.idreservation')
            ->andWhere('p.idmembre = :idmembre')
            ->andWhere('r.datereservation > :currentDate')
            ->andWhere('r.type NOT IN (:reservationTypes)')
            ->setParameter('idmembre', $idmembre)
            ->setParameter('currentDate', new \DateTime())
            ->setParameter('reservationTypes', ['Compte_desactive', 'Annulation'])
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre de réservations uniques par terrain, en considérant les critères spécifiés.
     *
     * @param int $idTerrain L'identifiant du terrain
     * @return int Le nombre de réservations uniques pour ce terrain
     */
    public function countUniqueReservationsByTerrain(int $idTerrain): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(DISTINCT r.idreservation)')
            ->andWhere('r.idterrain = :idTerrain')
            ->andWhere('r.type IN (:uniqueTypes)')
            ->setParameter('idTerrain', $idTerrain)
            ->setParameter('uniqueTypes', ['Postuler_Comme_Adversaire', 'Creer_Partie', 'Lancez_Vous'])
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.type = :a')
            ->setParameter('a', $type)
            ->getQuery()
            ->getResult();
    }
}
