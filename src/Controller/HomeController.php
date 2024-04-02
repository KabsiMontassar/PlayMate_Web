<?php

namespace App\Controller;

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
    #[Route('/', name: 'app_user_index', methods: ['GET', 'POST'])] 
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('app_user_login');
    }

    #[Route('/login', name: 'app_user_login', methods: ['GET' , 'POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        
        $user = new User();
        $formlogin = $this->createForm(Login::class , $user , ['validation_groups' => ['login']]);
        $formlogin->handleRequest($request);
        if ($formlogin->isSubmitted() && $formlogin->isValid()) {
           
            $user = $userRepository->findOneByemail($formlogin->get('email')->getData());

            if($user){
                if($user->getPassword() == $formlogin->get('password')->getData()){
                    return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
                }else{
                    $this->addFlash('error', 'Password is incorrect');
                }
                }
        
            }
        return $this->renderForm('login.html.twig', [
            
            'formlogin' => $formlogin,
          
        ]);
    }


    #[Route('/register', name: 'app_user_register', methods: ['GET' , 'POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);

        $user = new User();
        $form = $this->createForm(UserType::class , $user , ['validation_groups' => ['registration']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userexist = $userRepository->findOneByemail($form->get('email')->getData());
            if(!$userexist){
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
            }
            

            return $this->redirectToRoute('app_user_register', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('register.html.twig', [
            'form' => $form
            
        ]);
    }

    #[Route('/Apropos/{id}', name: 'app_Apropos', methods: ['GET', 'POST'])]
    public function Apropos( int $id , Request $request , EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);
    
        return $this->render('Front/apropos.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/Boutique/{id}', name: 'app_Boutique', methods: ['GET', 'POST'])]
    public function Boutique( int $id , Request $request , EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        return $this->render('Front/boutique.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/Contact/{id}', name: 'app_Contact', methods: ['GET', 'POST'])]
    public function Contact( int $id , Request $request , EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        return $this->render('Front/contact.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/Evenement/{id}', name: 'app_Evenement', methods: ['GET', 'POST'])]
    public function Evenement( int $id , Request $request , EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        return $this->render('Front/evenements.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/Home/{id}', name: 'app_Home', methods: ['GET', 'POST'])]
    public function Home(  Request $request , EntityManagerInterface $em ,int $id ): Response
    { 
    
        $user = $em->getRepository(User::class)->find($id);

        return $this->render('Front/index.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/Reservation/{id}', name: 'app_Reservation', methods: ['GET', 'POST'])]
    public function Reservation( int $id , Request $request , EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        return $this->render('Front/reservation.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/Service/{id}', name: 'app_Service', methods: ['GET', 'POST'])]
    public function Service( int $id , Request $request , EntityManagerInterface $em): Response
    {
       
        $user = $em->getRepository(User::class)->find($id);

        return $this->render('Front/service.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/Terrains/{id}', name: 'app_Terrains', methods: ['GET', 'POST'])]
    public function Terrains( int $id , Request $request , EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($id);

        return $this->render('Front/terrains.html.twig', [
            'user' => $user,
       
        ]);
    }



}


    
    
   
   
  

