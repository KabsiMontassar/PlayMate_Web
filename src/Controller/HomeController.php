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
        $userIdentifier = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
  
       
        $form1 = $this->createForm(UserUpdateType::class , $user , ['validation_groups' => ['update_profile']]);
        $form2 = $this->createForm(UserPasswordType::class  );
    
        // Handle form submissions
        $form1->handleRequest($request);
        $form2->handleRequest($request);
    
        // Check which form was submitted
        if ($form1->isSubmitted() && $form1->isValid()) {
 
        
          
            $formData = $form1->getData();
            // Process form 1 (update user information)
           $imageFile =  $form1->get('imageFile')->getData();
           if ($imageFile) {
            // Generate a unique name for the file
            $newFilename = uniqid().'.'.$imageFile->guessExtension();

    
            // Move the file to the desired directory
            $imageFile->move(
                $this->getParameter('image_directory'), // Path defined in services.yaml or config/packages/framework.yaml
                $newFilename
            );
           
            $formData->setImage($newFilename);
        }

            $entityManager->persist($formData);
            $entityManager->flush();
           


        }
      
        if ($form2->isSubmitted() && $form2->isValid()) {


            if ($form2->get('NewPassword')->getData() == $form2->get('ConfirmPassword')->getData()) {

                if($encoder->isPasswordValid($user, $form2->get('CurrentPassword')->getData())){
                    $user->setPassword($encoder->encodePassword($user, $form2->get('NewPassword')->getData()));
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $this->addFlash('success', 'Password updated successfully');
                } else {
                    $this->addFlash('danger', 'Current password is incorrect');
                }

            
            } else {
                $this->addFlash('danger', 'New password and confirm password do not match');

            }
           
            // Process form 2 (update user password)
           if($form2->get('CurrentPassword')->getData() == $user ->getPassword()){


              if($form2->get('NewPassword')->getData() == $form2->get('ConfirmPassword')->getData()){
                
                $user->setPassword($form2->get('NewPassword')->getData());
                $entityManager->persist($user);
                $entityManager->flush();
                          
              }else{
                   $this->addFlash('danger', 'New password and confirm password do not match');
                  
              }
        }
        
    }
    
      
        return $this->render('Back/GestionUser/userProfile.html.twig', [
            'form1' => $form1->createView(),
            'form2' => $form2->createView(),
            'user' => $user,
        ]);
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


    
    
   
   
  

