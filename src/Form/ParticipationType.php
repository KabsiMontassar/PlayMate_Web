<?php

namespace App\Form;

use App\Entity\Participation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityManagerInterface; 

class ParticipationType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        // Utiliser l'EntityManager pour construire la requête SQL
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();
        $queryBuilder
            ->select('e.nomEquipe')
            ->from('membreparequipe', 'me')
            ->innerJoin('me', 'equipe', 'e', 'me.idEquipe = e.idEquipe')
            ->where('me.idMembre = :idmembre')
            ->setParameter('idmembre', $user->getId()); 

        $stmt = $queryBuilder->execute();
        $equipes = $stmt->fetchAllAssociative();

        $equipeChoices = [];
        foreach ($equipes as $equipe) {
            $equipeChoices[$equipe['nomEquipe']] = $equipe['nomEquipe'];
        }

        $builder
        ->add('idtournoi')
        ->add('nomequipe', ChoiceType::class, [
                'choices' => $equipeChoices,
            ])

           

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participation::class,
            'user' => null, // Ajouter une option pour passer l'utilisateur
        ]);
    }
}
