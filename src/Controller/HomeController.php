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


class HomeController extends AbstractController
{
    #[Route('/', name: 'start', methods: ['GET', 'POST'])] 
    public function index(): RedirectResponse
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


        return $this->render('Front/evenements.html.twig', [
          
            'tournois' => $tournois,
            
       
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


    // #[Route('/login', name: 'app_user_login', methods: ['GET' , 'POST'])]
    // public function login(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $userRepository = $entityManager->getRepository(User::class);
        
    //     $user = new User();
    //     $formlogin = $this->createForm(Login::class , $user , ['validation_groups' => ['login']]);
    //     $formlogin->handleRequest($request);
    //     if ($formlogin->isSubmitted() && $formlogin->isValid()) {
           
    //         $user = $userRepository->findOneByemail($formlogin->get('email')->getData());

    //         if($user){
    //             if($user->getPassword() == $formlogin->get('password')->getData()){
    //                 return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
    //             }else{
    //                 $this->addFlash('error', 'Password is incorrect');
    //             }
    //             }
        
    //         }
    //     return $this->renderForm('login.html.twig', [
            
    //         'formlogin' => $formlogin,
          
    //     ]);
    // }


    // #[Route('/register', name: 'app_user_register', methods: ['GET' , 'POST'])]
    // public function register(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $userRepository = $entityManager->getRepository(User::class);

    //     $user = new User();
    //     $form = $this->createForm(UserType::class , $user , ['validation_groups' => ['registration']]);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $userexist = $userRepository->findOneByemail($form->get('email')->getData());
    //         if(!$userexist){
    //             $entityManager->persist($user);
    //             $entityManager->flush();
    //             return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
    //         }
            

    //         return $this->redirectToRoute('app_user_register', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('register.html.twig', [
    //         'form' => $form
            
    //     ]);
    // }


}


    
    
   
   
  

