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



public function findByUser(User $user): array
{
    try {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT e
            FROM App\Entity\Equipe e
            JOIN App\Entity\Membreparequipe m
            WITH e = m.idequipe
            WHERE m.idmembre = :user'
        )->setParameter('user', $user);

        return $query->getResult();
    } catch (\Exception $e) {
        // Handle the exception, log it, or rethrow it as needed
        throw new \RuntimeException('An error occurred while fetching data: ' . $e->getMessage());
    }
}

public function findByEquipe(Equipe $equipe): array
{
  
   
    try {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT m
            FROM App\Entity\Membreparequipe m
            WHERE m.idequipe = :equipe'
          
        )->setParameter('equipe', $equipe);
        return $query->getResult();
    } catch (\Exception $e) {
        // Handle the exception, log it, or rethrow it as needed
        throw new \RuntimeException('An error occurred while fetching data: ' . $e->getMessage());
    }
}

public function findOneMembreparequipe(User $user,Equipe $equipe ): Membreparequipe
 {
    try {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT m
            FROM App\Entity\Membreparequipe m
            WHERE m.idmembre = :user AND m.idequipe = :equipe'
        )->setParameter('user', $user)
        ->setParameter('equipe', $equipe);

        return $query->getOneOrNullResult();
    } catch (\Exception $e) {
        // Handle the exception, log it, or rethrow it as needed
        throw new \RuntimeException('An error occurred while fetching data: ' . $e->getMessage());
    }

 }




}
