<?php

namespace App\Controller;
use App\Entity\Tournoi;
use App\Entity\Terrain;
use App\Entity\Avis;

use App\Entity\Commande;
use App\Entity\User;
use App\Entity\Product;
use App\Form\UserType;

use App\Form\Login;
use App\Repository\UserRepository;

use App\Form\forgetpassword;
use App\Repository\TerrainRepository;
use App\Repository\TournoiRepository;
use App\Repository\AvisRepository;

    
use App\Entity\Equipe;
use App\Entity\Membreparequipe;
use App\Repository\ProductRepository;
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
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
 
use MercurySeries\FlashyBundle\FlashyNotifier;
//not found 







#[Route('/profile')]
class ProfileController extends AbstractController
{
   
    #[Route('/', name: 'First', methods: ['GET', 'POST'])] 
    public function index(Security $security, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, Request $request, TournoiRepository $tournoiRepository): Response
    {
        $user = $security->getUser();
        if($user == null){
            return $this->redirectToRoute('app_login');
        }
        $userIdentifier = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
        $terrains = null;
        $tournois = null;
        $participationsById = null;
        $avis = null;
        $teams=null;
        $teamsWithMembers = null;	
        $nonce = bin2hex(random_bytes(16));
        $avisCounts = [];

        if($user->getRole()== 'Membre'){
            $teams = $entityManager->getRepository(Equipe::class)->findByUser($user);
            $teamsWithMembers = [];
            foreach ($teams as $team) {
                $members = $entityManager->getRepository(Membreparequipe::class)->findBy(['idequipe' => $team]);
                $teamsWithMembers[$team->getNomequipe()] = $members;
            }
        }
        
      
      
        if($user->getRole() == 'Proprietaire de Terrain'){
            $terrains = $entityManager->getRepository(Terrain::class)->findBy(['idprop' => $user]);
            $avis = $entityManager->getRepository(Avis::class)->findAll();
            foreach ($terrains as $terrain) {
                $avisCounts[$terrain->getId()] = count($terrain->getAvis());
        }
       
    }
        

        if($user->getRole() == 'Organisateur'){
            $tournois = $entityManager->getRepository(Tournoi::class)->findBy(['idorganisateur' => $user]);
            $participations = $tournoiRepository->countParticipationsForEachTournoi();
            $participationsById = [];
            foreach ($participations as $participation) {
                $participationsById[$participation['id']] = $participation['nombre_participations'];
            }
    
        }

        if($user->getRole() == 'Fournisseur'){
           
            $products = $entityManager->getRepository(Product::class)->findBy(['idfournisseur' => $user]);

            $commandes = [];
            foreach ($products as $product) {
                $commandes[$product->getId()] = $entityManager->getRepository(Commande::class)->findBy(['idproduit' => $product->getId()]);
            }
        }
       



        if($user->getRole() == 'Proprietaire de Terrain'){
            $terrains = $entityManager->getRepository(Terrain::class)->findBy(['idprop' => $user]);
        }
            return $this->render('userBase.html.twig',[
                'terrains' => $terrains,
                'tournois' => $tournois,
                'avis' => $avis,
                'user' => $user,
                'teams' => $teams,
                'teamsWithMembers' => $teamsWithMembers,
                'nonce' => $nonce,
                'participationsById' => $participationsById,
                'avisCounts' => $avisCounts,
                'products' =>$products, 
                'commandes' => $commandes,
            ]);
        
      
    }

    #[Route('/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(FlashyNotifier $flashy,Security $security, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, Request $request): Response
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
           

            $flashy->success('Updated Succefully!');
            
            return $this->redirectToRoute('First');


        }
      
        if ($form2->isSubmitted() && $form2->isValid()) {


            if ($form2->get('NewPassword')->getData() == $form2->get('ConfirmPassword')->getData()) {

                if($encoder->isPasswordValid($user, $form2->get('CurrentPassword')->getData())){
                    $user->setPassword($encoder->encodePassword($user, $form2->get('NewPassword')->getData()));
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $flashy->success('Password updated successfully');
                   
                } else {
                    $flashy->error('Current password is incorrect');
                }

            
            } else {
                $flashy->error('New password and confirm password do not match');

            }
           
            // Process form 2 (update user password)
           if($form2->get('CurrentPassword')->getData() == $user ->getPassword()){


              if($form2->get('NewPassword')->getData() == $form2->get('ConfirmPassword')->getData()){
                
                $user->setPassword($form2->get('NewPassword')->getData());
                $entityManager->persist($user);
                $entityManager->flush();
                          
              }else{
                $flashy->error('New password and confirm password do not match');
                  
              }
              return $this->redirectToRoute('First');
        }
        else{
            $flashy->error('Current password is incorrect');
        }
    }
    
   
      
        return $this->render('Front/ProfileElements/Forms/FormEdit.html.twig', [
            'form1' => $form1->createView(),
            'form2' => $form2->createView(),
            'user' => $user,
           
           
        ]);
    }



}


    
    
   
   
  

