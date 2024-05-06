<?php

namespace App\Controller;


use App\Entity\Tournoi;
use App\Entity\Terrain;
use App\Entity\Categorie;
use App\Entity\Product;
use App\Entity\User;
use App\Form\UserType;

use App\Form\Login;
use App\Repository\UserRepository;
use App\Repository\TerrainRepository;


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
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Historique;

use App\Controller\Payment;


use App\Repository\HistoriqueRepository;
use Symfony\Component\Serializer\SerializerInterface;


use App\Repository\ReservationRepository;
use BaconQrCode\Encoder\QrCode;
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

    public function __construct(EntityManagerInterface $entityManager, Security $security , SerializerInterface $serializer)
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
    public function Boutique( EntityManagerInterface $em ): Response
    {

        $productRepository = $em->getRepository(Product::class);
        $categorieRepository = $em->getRepository(Categorie::class);
        $products = $productRepository->findAll();
        $categories = $categorieRepository->findAll();
        $string = implode(' ', $products);
        // $qrCode = $qrcodeService->qrcode($string);
        return $this->render('Front/boutique.html.twig', [
          
            'products' => $products,
            'categories' => $categories,
            // 'qrCode' => $qrCode,
        ]);
          
    }
    #[Route('/Contact', name: 'app_Contact', methods: ['GET', 'POST'])]
    public function Contact(EntityManagerInterface $em): Response
    {


        return $this->render('Front/contact.html.twig', []);
    }
    #[Route('/Evenement', name: 'app_Evenement', methods: ['GET', 'POST'])]
    public function Evenement(EntityManagerInterface $entityManager,  PaginatorInterface $paginator , Request $request): Response
    {
        $queryBuilder = $entityManager->getRepository(Tournoi::class)->createQueryBuilder('t');

        // Optionally add some filters to the query builder if needed
        // For example, if you want to filter by some criteria
        // $queryBuilder->where('t.someField = :value')->setParameter('value', $value);

        // Get the query from the query builder
        $query = $queryBuilder->getQuery();

        // Paginate the results of the query
        $pagination = $paginator->paginate(
            $query, // query NOT result
            $request->query->getInt('page', 1), // page number, 1 if not set
            5 // limit per page
        );


$tournois = $entityManager
            ->getRepository(Tournoi::class)
            ->findAll();
        // Get the most recent 2 tournois separately if needed
        $recentTournois = $entityManager->getRepository(Tournoi::class)->findBy([], ['id' => 'DESC'], 2);

        return $this->render('Front/evenements.html.twig', [
            'pagination' => $pagination,
            'tournoirecent' => $recentTournois,
            'tournois' => $tournois,
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
    /***************************************************************************** */
    #[Route('/Terrains', name: 'app_Terrains', methods: ['GET', 'POST'])]
public function Terrains(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request, TerrainRepository $terrainRepository): Response
{
    // Get search query parameter
    $searchQuery = $request->query->get('query');
    // Get order query parameter
    $order = $request->query->get('order');

    // Get filtered terrains
    $queryBuilder = $terrainRepository->createQueryBuilder('t')
        ->where('t.status = :status')
        ->setParameter('status', true);

    if ($searchQuery) {
         $queryBuilder->andWhere('t.address LIKE :search')
            ->orWhere('t.gouvernorat LIKE :search')
            ->setParameter('search', '%' . $searchQuery . '%');
    }
    if ($order === 'price_asc') {
        $terrains = $terrainRepository->findAllOrderByPrice('ASC');
        $queryBuilder = $terrains;
    } elseif ($order === 'price_desc') {
        $terrains = $terrainRepository->findAllOrderByPrice('DESC');
        $queryBuilder = $terrains;
    } elseif ($order === 'duration_asc') {
        $terrains = $terrainRepository->findAllOrderByDuration('ASC');
        $queryBuilder = $terrains;
    } elseif ($order === 'duration_desc') {
        $terrains = $terrainRepository->findAllOrderByDuration('DESC');
        $queryBuilder = $terrains;
    }  
    $query = $queryBuilder->getQuery();

    // Paginate the results
    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        2 // Items per page
    );

    return $this->render('Front/terrains.html.twig', [
        'pagination' => $pagination,
    ]);
}
    /***************************************************************************** */

    
    #[Route('/Historique', name: 'app_Historique', methods: ['GET', 'POST'])]
    public function Historique(Security $security, HistoriqueRepository $historiqueRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();
        if ($user == null) {
            return $this->redirectToRoute('app_login');
        } else {

            $userIdentifier = $security->getUser()->getUserIdentifier();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);

            $historiques = $historiqueRepository->ListHistoriqueParMembre($user->getId());

            return $this->render('Front/historique.html.twig', [
                'historiques' => $historiques,
            ]);
        }
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
        $user = $security->getUser();
        if ($user == null) {
            return $this->redirectToRoute('app_login');
        } else {
            $userIdentifier = $security->getUser()->getUserIdentifier();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
            // ajout iduser
            $futureReservations = $reservationRepository->findFutureReservationsForMember($user->getId());


            return $this->render('Front/consulterReservation.html.twig', [
                'reservation' => $futureReservations,
            ]);
        }
    }


    #[Route('/increment-visits/{id}', name: 'app_increment_visits', methods: ['POST'])]
    public function incrementVisits(Tournoi $tournoi, EntityManagerInterface $entityManager): Response
    {
        $user = $this->security->getUser();

        if ($user && $user->getRole() == 'Membre') {
            $tournoi->setVisite($tournoi->getVisite() + 1);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false, 'message' => 'User is not a member']);
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
