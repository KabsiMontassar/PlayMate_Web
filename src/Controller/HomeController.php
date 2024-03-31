<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Terrain;

use App\Form\Login;

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
        return $this->redirectToRoute('app_Home');
    }

    #[Route('/Apropos', name: 'app_Apropos', methods: ['GET', 'POST'])]
    public function Apropos(?int $id, EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find(id);
        // if ($id !== null) {
        //     $userRepository = $entityManager->getRepository(User::class);
        //     $user = $userRepository->find(43);
        //     if (!$user) {
        //         // User not found, return an empty user object or any default value
        //         $user = new User(); // Assuming User is an entity class
        //         // You can set default values for the user object if needed
        //     }
        // } else {
        //     // If $id is null (no user ID provided), treat as a guest
        //     $user = null;
        // }
        return $this->render('Front/apropos.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/Boutique', name: 'app_Boutique', methods: ['GET', 'POST'])]
    public function Boutique(?int $id, EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find(43);
        // if ($id !== null) {
        //     $userRepository = $entityManager->getRepository(User::class);
        //     $user = $userRepository->find(43);
        //     if (!$user) {
        //         // User not found, return an empty user object or any default value
        //         $user = new User(); // Assuming User is an entity class
        //         // You can set default values for the user object if needed
        //     }
        // } else {
        //     // If $id is null (no user ID provided), treat as a guest
        //     $user = null;
        // }
        return $this->render('Front/boutique.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/Contact', name: 'app_Contact', methods: ['GET', 'POST'])]
    public function Contact(?int $id, EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find(43);
        // if ($id !== null) {
        //     $userRepository = $entityManager->getRepository(User::class);
        //     $user = $userRepository->find(43);
        //     if (!$user) {
        //         // User not found, return an empty user object or any default value
        //         $user = new User(); // Assuming User is an entity class
        //         // You can set default values for the user object if needed
        //     }
        // } else {
        //     // If $id is null (no user ID provided), treat as a guest
        //     $user = null;
        // }
        return $this->render('Front/contact.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/Evenement', name: 'app_Evenement', methods: ['GET', 'POST'])]
    public function Evenement(?int $id, EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find(43);
        // if ($id !== null) {
        //     $userRepository = $entityManager->getRepository(User::class);
        //     $user = $userRepository->find(43);
        //     if (!$user) {
        //         // User not found, return an empty user object or any default value
        //         $user = new User(); // Assuming User is an entity class
        //         // You can set default values for the user object if needed
        //     }
        // } else {
        //     // If $id is null (no user ID provided), treat as a guest
        //     $user = null;
        // }
        return $this->render('Front/evenements.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/Home', name: 'app_Home', methods: ['GET', 'POST'])]
    public function Home(?int $id, EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find(43);
        return $this->render('Front/index.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/Reservation', name: 'app_Reservation', methods: ['GET', 'POST'])]
    public function Reservation(?int $id, EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find(43);
        // if ($id !== null) {
        //     $userRepository = $entityManager->getRepository(User::class);
        //     $user = $userRepository->find(43);
        //     if (!$user) {
        //         // User not found, return an empty user object or any default value
        //         $user = new User(); // Assuming User is an entity class
        //         // You can set default values for the user object if needed
        //     }
        // } else {
        //     // If $id is null (no user ID provided), treat as a guest
        //     $user = null;
        // }
        return $this->render('Front/reservation.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/Service', name: 'app_Service', methods: ['GET', 'POST'])]
    public function Service(?int $id, EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find(43);
        // if ($id !== null) {
        //     $userRepository = $entityManager->getRepository(User::class);
        //     $user = $userRepository->find(43);
        //     if (!$user) {
        //         // User not found, return an empty user object or any default value
        //         $user = new User(); // Assuming User is an entity class
        //         // You can set default values for the user object if needed
        //     }
        // } else {
        //     // If $id is null (no user ID provided), treat as a guest
        //     $user = null;
        // }
        return $this->render('Front/service.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/Terrains', name: 'app_Terrains', methods: ['GET', 'POST'])]
public function Terrains(?int $id, EntityManagerInterface $entityManager): Response
{
    $terrainRepository = $entityManager->getRepository(Terrain::class);
    $terrains = $terrainRepository->findAll();

    $userRepository = $entityManager->getRepository(User::class);
    $user = $userRepository->find(43);

    return $this->render('Front/terrains.html.twig', [
        'user' => $user,
        'terrains' => $terrains,
    ]);
}





}


    
    
   
   
  

