<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserUpdateType;
use App\Form\UserPasswordType;
use App\Form\Login;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    #[Route('/dashboard', name: 'app_user_dashboard', methods: ['GET'])]
    public function dashboard(UserRepository $userRepository): Response
    {
        return $this->render('user/dashboard.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/login', name: 'app_user_login', methods: ['GET' , 'POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        
        $user = new User();
        $formlogin = $this->createForm(Login::class );
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
        $form = $this->createForm(UserType::class , $user);
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

  
    #[Route('/{id}/profile', name: 'app_user_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request,int $id, EntityManagerInterface $entityManager): Response
    {
        
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($id);
        $form1 = $this->createForm(UserUpdateType::class , $user);
        $form2 = $this->createForm(UserPasswordType::class  	);
    
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

           
        
            $userRepository = $entityManager->getRepository(User::class);
            $user = $userRepository->find($id);
      
       
            // Process form 2 (update user password)
           if($form2->get('CurrentPassword')->getData() == $user ->getPassword()){


              if($form2->get('NewPassword')->getData() == $form2->get('ConfirmPassword')->getData()){
                
                $user->setPassword($form2->get('NewPassword')->getData());
                $entityManager->persist($user);
                $entityManager->flush();
                          
              }else{
                   dd('Password does not match');
                  
              }
        }
        else{
            dd('Current password is incorrect');
        }
    }
    
      
        return $this->render('user/userProfile.html.twig', [
            'form1' => $form1->createView(),
            'form2' => $form2->createView(),
            'user' => $user,
        ]);
    }
    



}

