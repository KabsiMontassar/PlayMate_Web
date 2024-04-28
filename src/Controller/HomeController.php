<?php

namespace App\Controller;


use App\Entity\Tournoi;
use App\Entity\Terrain;
use App\Entity\User;
use App\Form\UserType;

use App\Form\Login;
use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\UserUpdateType;
use App\Form\UserPasswordType;
use Symfony\Component\Runtime\Runner\Symfony\ResponseRunner;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Historique;

use App\Controller\Payment;

use App\Repository\HistoriqueRepository;
use Symfony\Component\Serializer\SerializerInterface;


use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManager;

class HomeController extends AbstractController
{
    #[Route('/', name: 'start', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this->redirectToRoute('app_Home');
    }


    private $serializer;

    private $entityManager;
    private $security;

    private $liveScoreService;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function __construct2(EntityManagerInterface $entityManager, Security $security, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->serializer = $serializer;
    }

    #[Route('/Apropos', name: 'app_Apropos', methods: ['GET', 'POST'])]
    public function Apropos(EntityManagerInterface $em): Response
    {

        return $this->render('Front/apropos.html.twig', []);
    }
    #[Route('/Boutique', name: 'app_Boutique', methods: ['GET', 'POST'])]
    public function Boutique(EntityManagerInterface $em): Response
    {


        return $this->render('Front/boutique.html.twig', []);
    }
    #[Route('/Contact', name: 'app_Contact', methods: ['GET', 'POST'])]
    public function Contact(EntityManagerInterface $em): Response
    {


        return $this->render('Front/contact.html.twig', []);
    }
    #[Route('/Evenement', name: 'app_Evenement', methods: ['GET', 'POST'])]
    public function Evenement(EntityManagerInterface $entityManager): Response
    {
        $tournois = $entityManager
            ->getRepository(Tournoi::class)
            ->findAll();

        $recentTournois =  $entityManager
            ->getRepository(Tournoi::class)
            ->findBy([], ['id' => 'DESC'], 2);


        return $this->render('Front/evenements.html.twig', [

            'tournois' => $tournois,
            'tournoirecent'  => $recentTournois,


        ]);
    }



    #[Route('/Home', name: 'app_Home', methods: ['GET', 'POST'])]
    public function Home(EntityManagerInterface $entityManager): Response
    {
        return $this->render('Front/index.html.twig', []);
    }
    #[Route('/Reservation', name: 'app_Reservation', methods: ['GET', 'POST'])]
    public function Reservation(EntityManagerInterface $entityManager): Response
    {
        $terrainRepository = $entityManager->getRepository(Terrain::class);
        $terrains = $terrainRepository->findAll();

        return $this->render('Front/reservation.html.twig', [

            'terrains' => $terrains,




        ]);
    }
    #[Route('/Service', name: 'app_Service', methods: ['GET', 'POST'])]
    public function Service(EntityManagerInterface $entityManager): Response
    {


        return $this->render('Front/service.html.twig', []);
    }
    #[Route('/Terrains', name: 'app_Terrains', methods: ['GET', 'POST'])]
    public function Terrains(EntityManagerInterface $entityManager): Response
    {


        $terrainRepository = $entityManager->getRepository(Terrain::class);
        $terrains = $terrainRepository->findAll();

        return $this->render('Front/terrains.html.twig', [

            'terrains' => $terrains,


        ]);
    }
    #[Route('/Historique', name: 'app_Historique', methods: ['GET', 'POST'])]
    public function Historique(Security $security, HistoriqueRepository $historiqueRepository, EntityManagerInterface $entityManager): Response
    {

        $userIdentifier = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);

        $historiques = $historiqueRepository->ListHistoriqueParMembre($user->getId());

        return $this->render('Front/historique.html.twig', [
            'historiques' => $historiques,
        ]);
    }
    #[Route('/game', name: 'game')]
    public function jeu(): Response
    {
        return $this->render('Front/jeuPlayMate.html.twig');
    }


    // ajout id
    #[Route('/FutureReservations', name: 'app_reservation_future', methods: ['GET'])]
    public function getFuturReservationsByIdUser(Security $security, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager): Response
    {
        $userIdentifier = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
        // ajout iduser
        $futureReservations = $reservationRepository->findFutureReservationsForMember($user->getId());


        return $this->render('Front/consulterReservation.html.twig', [
            'reservation' => $futureReservations,
        ]);
    }





    #[Route('/increment-visits/{id}', name: 'app_increment_visits', methods: ['POST'])]
    public function incrementVisits(Tournoi $tournoi, EntityManagerInterface $entityManager): Response
    {
        $tournoi->setVisite($tournoi->getVisite() + 1);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    /*
#[Route('/increment-unique-visit/{id}', name: 'app_increment_unique_visit', methods: ['POST'])]
public function incrementUniqueVisit(Tournoi $tournoi, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
{
    $user = $this->getUser(); // Récupérer l'utilisateur connecté
    if (!$user) {
        return new JsonResponse(['success' => false, 'message' => 'User not logged in']);
    }
    
    $visite = $entityManager->getRepository(TournoiVisite::class)->findOneBy(['tournoi' => $tournoi, 'user' => $user]);
    
    if (!$visite) {
        $visite = new TournoiVisite();
        $visite->setTournoi($tournoi);
        $visite->setUser($user);
        $entityManager->persist($visite);
        $tournoi->setVisites($tournoi->getVisites() + 1);
        $entityManager->flush();
    }
    
    return new JsonResponse(['success' => true]);
}*/
}
