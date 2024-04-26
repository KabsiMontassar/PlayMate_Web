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


class HomeController extends AbstractController
{
    #[Route('/', name: 'start', methods: ['GET', 'POST'])] 
    public function index(): Response
    {
     return $this->redirectToRoute('app_Home');
    }

    #[Route('/Apropos', name: 'app_Apropos', methods: ['GET', 'POST'])]
    public function Apropos(EntityManagerInterface $em): Response
    {
    
        return $this->render('Front/apropos.html.twig', [
          
        ]);
    }
    #[Route('/Boutique', name: 'app_Boutique', methods: ['GET', 'POST'])]
    public function Boutique( EntityManagerInterface $em): Response
    {
     

        return $this->render('Front/boutique.html.twig', [
          
           
            
       
        ]);
    }
    #[Route('/Contact', name: 'app_Contact', methods: ['GET', 'POST'])]
    public function Contact(EntityManagerInterface $em): Response
    {
       

        return $this->render('Front/contact.html.twig', [
          
           
            
       
        ]);
    }
    #[Route('/Evenement', name: 'app_Evenement', methods: ['GET', 'POST'])]
    public function Evenement( EntityManagerInterface $entityManager): Response
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
    public function Home(  EntityManagerInterface $entityManager): Response
    { 
        return $this->render('Front/index.html.twig', [
          
           
            
       
        ]);
    }
    #[Route('/Reservation', name: 'app_Reservation', methods: ['GET', 'POST'])]
    public function Reservation( EntityManagerInterface $entityManager): Response
    {
        $terrainRepository = $entityManager->getRepository(Terrain::class);
        $terrains = $terrainRepository->findAll();

        return $this->render('Front/reservation.html.twig', [
             
                'terrains' => $terrains,
          
           
            
       
        ]);
    }
    #[Route('/Service', name: 'app_Service', methods: ['GET', 'POST'])]
    public function Service( EntityManagerInterface $entityManager): Response
    {
       

        return $this->render('Front/service.html.twig', [
          
           
            
       
        ]);
    }
    #[Route('/Terrains', name: 'app_Terrains', methods: ['GET', 'POST'])]
    public function Terrains( EntityManagerInterface $entityManager): Response
    {
       

        $terrainRepository = $entityManager->getRepository(Terrain::class);
        $terrains = $terrainRepository->findAll();

        return $this->render('Front/terrains.html.twig', [
          
            'terrains' => $terrains,
            
       
        ]);
    }



}


    
    
   
   
  

