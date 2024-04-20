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
#[Route('/Profle')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'First', methods: ['GET', 'POST'])] 
    public function index(): Response
    {
        return $this->render('userBase.html.twig');
    }

    
    #[Route('/OverView', name: 'app_OverView', methods: ['GET', 'POST'])]
    public function OverView(): Response
    {
       
        return $this->render('Front/ProfileElements/userProfile.html.twig');
    }
    #[Route('/Tournoi', name: 'app_Tournoi_Profile', methods: ['GET', 'POST'])]
    public function Tournoi( ): Response
    {
       

        return $this->render('Front/ProfileElements/userTournoi.html.twig');
    }
    #[Route('/Terrain', name: 'app_Terrains_Profile', methods: ['GET', 'POST'])]
    public function Terrain(): Response
    {
    
        return $this->render('Front/ProfileElements/userTerrain.html.twig');
    }
    #[Route('/Participation', name: 'app_Participation_Profile', methods: ['GET', 'POST'])]
    public function participation(): Response
    {
    
        return $this->render('Front/ProfileElements/userParticipation.html.twig');
    }

    #[Route('/Reservation', name: 'app_Reservation_Profile', methods: ['GET', 'POST'])]
    public function Reservation(): Response
    {
    
        return $this->render('Front/ProfileElements/userReservation.html.twig');
    }
    #[Route('/Produit', name: 'app_Produit_Profile', methods: ['GET', 'POST'])]
    public function Produit(): Response
    {
    
        return $this->render('Front/ProfileElements/userProduit.html.twig');
    }
    #[Route('/Commande', name: 'app_Commande_Profile', methods: ['GET', 'POST'])]

    public function Commande(): Response
    {
    
        return $this->render('Front/ProfileElements/userCommande.html.twig');
    }



}


    
    
   
   
  

