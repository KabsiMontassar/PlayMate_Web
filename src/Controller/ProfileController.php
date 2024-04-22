<?php

namespace App\Controller;
use App\Entity\Tournoi;
use App\Entity\Terrain;
use App\Entity\User;
use App\Form\UserType;

use App\Form\Login;
use App\Repository\UserRepository;

use App\Form\forgetpassword;
use App\Repository\TerrainRepository;
use App\Repository\TournoiRepository;
    
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
#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'First', methods: ['GET', 'POST'])] 
    public function index(Security $security, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, Request $request): Response
    {
        $user = $security->getUser();
        if($user == null){
            return $this->redirectToRoute('app_login');
        }
        $userIdentifier = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
  
        $terrains = $entityManager->getRepository(Terrain::class)->findBy(['idprop' => $user]);

            return $this->render('userBase.html.twig',[
              
                'user' => $user,
                'terrains' => $terrains
            ]);
        
      
    }

    #[Route('/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(Security $security, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, Request $request): Response
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
    
      
        return $this->render('Front/ProfileElements/Forms/FormEdit.html.twig', [
            'form1' => $form1->createView(),
            'form2' => $form2->createView(),
            'user' => $user
           
        ]);
    }



}


    
    
   
   
  

