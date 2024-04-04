<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserUpdateType;
use App\Form\UserPasswordType;
use App\Form\forgetpassword;
use App\Form\Login;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Controller\HomeController;
use Symfony\Component\Security\Core\Security;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('Back/GestionUser/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    #[Route('/dashboard', name: 'app_user_dashboard', methods: ['GET'])]
    public function dashboard(UserRepository $userRepository): Response
    {
        return $this->render('Back/GestionUser/dashboard.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

   

  
    #[Route('/profile', name: 'app_user_profile', methods: ['GET', 'POST'])]
    public function profile(UserPasswordEncoderInterface $encoder,Security $security,Request $request, EntityManagerInterface $entityManager): Response
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
           
            $formData->setImage($newFilename);
        }

            $entityManager->persist($formData);
            $entityManager->flush();
            $imageFile->move(
                $this->getParameter('image_directory'), // Path defined in services.yaml or config/packages/framework.yaml
                $newFilename
            );

            $this->addFlash('success', 'Profile updated successfully');

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
        else{
        }
    }
    
      
        return $this->render('Back/GestionUser/userProfile.html.twig', [
            'form1' => $form1->createView(),
            'form2' => $form2->createView(),
            'user' => $user,
        ]);
    }


    #[Route('/forgot_password', name: 'app_forgot_password', methods: ['GET', 'POST'])]

    public function forgetPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(forgetpassword::class);
        $form->handleRequest($request);


    
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $entityManager->getRepository(User::class)->findOneByEmail($form->get('email')->getData());
           
            
            if (!$user) {
                $this->addFlash('danger', 'Email not found');
                dd($user);
                return $this->redirectToRoute('app_forgot_password');
            }

            if ($user->getVerificationCode() != $form->get('verificationCode')->getData()) {
                $this->addFlash('danger', 'Verification code is incorrect');
                return $this->redirectToRoute('app_forgot_password');
            }
    
            if ($form->get('Newpassword')->getData() != $form->get('Confirmpassword')->getData()) {
                $this->addFlash('danger', 'New password and confirm password do not match');
                return $this->redirectToRoute('app_forgot_password');
            }
    
            $user->setPassword($encoder->encodePassword($user, $form->get('Confirmpassword')->getData()));
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Password updated successfully');
            return $this->redirectToRoute('app_login');
        }
    
        return $this->render('Back/GestionUser/ForgetPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    


// #[Route('/check-email', name: 'check_email', methods: ['POST'])]
// public function checkEmailValidity(Request $request): Response
// {
//     $email = $request->request->get('email');
//     // Perform validation logic here (e.g., check if the email exists in the database)

//     // Dummy validation logic (replace with your actual validation logic)
//     if ($email === 'valid@example.com') {
//         return new Response('valid', Response::HTTP_OK);
//     } else {
//         return new Response('invalid', Response::HTTP_BAD_REQUEST);
//     }
// }



}

