<?php

namespace App\Repository;

use App\Entity\Equipe;
use App\Entity\User;
use App\Entity\Membreparequipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equipe>
 *
 * @method Equipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipe[]    findAll()
 * @method Equipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipe::class);
    }

//    /**
//     * @return Equipe[] Returns an array of Equipe objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Equipe
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findEquipeByUser($idmembre): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT e
            FROM App\Entity\Equipe e
            JOIN App\Entity\Membreparequipe m
            WHERE m.idmembre = :idmembre'
        )->setParameter('idmembre', $idmembre);

        return $query->getResult();
    }

// give me a function that takes a equipe and returns all the users associated by the table membreparequipe 

 public function findUsersbyEquipe($idequipe): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT u
            FROM App\Entity\User u
            JOIN App\Entity\Membreparequipe m
            WHERE m.idequipe = :idequipe'
        )->setParameter('idequipe', $idequipe);

        return $query->getResult();
    }


}
