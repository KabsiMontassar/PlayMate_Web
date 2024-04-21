<?php

namespace App\Form;

use App\Entity\Participation;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Component\Security\Core\Security;

class ParticipationType extends AbstractType
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
{ 
    // Accessing the class property `$security` correctly
    $userIdentifier = $this->security->getUser()->getUserIdentifier();
    // Accessing the class property `$entityManager` correctly
    $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);

    // Constructing the SQL query using the entity manager connection
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
    ->add('nomequipe', ChoiceType::class, [
        'choices' => $equipeChoices,
    ]);
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participation::class,
            'user' => null, // Ajouter une option pour passer l'utilisateur
        ]);
    }
}
