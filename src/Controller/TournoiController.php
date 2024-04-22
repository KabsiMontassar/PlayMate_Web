<?php

namespace App\Controller;

use App\Entity\Tournoi;
use App\Entity\User;
use App\Entity\Participation;
use App\Form\TournoiType;
use App\Form\ParticipationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use App\Service\WeatherService;


#[Route('/tournoi')]
class TournoiController extends AbstractController
{
    #[Route('/', name: 'app_tournoi_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tournois = $entityManager
            ->getRepository(Tournoi::class)
            ->findAll();

        return $this->render('Back/GestionEvenement/tournoi/tournoi.html.twig', [
            'tournois' => $tournois,
        ]);
    }


    #[Route('/profile', name: 'app_user_tournoi')]
public function userTournoi(Security $security, EntityManagerInterface $entityManager): Response
{
    $userIdentifier = $security->getUser()->getUserIdentifier();
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);

    $tournois = $entityManager->getRepository(Tournoi::class)->findBy(['idorganisateur' => $user]);
    
    // Render the template with the tournaments
    return $this->render('Back/GestionEvenement/tournoi/profiletournoi.html.twig', [
        'tournois' => $tournois,
    ]);
}

    #[Route('/new', name: 'app_tournoi_new', methods: ['GET', 'POST'])]
    public function new(Security $security, Request $request, EntityManagerInterface $entityManager): Response
    {
    $userIdentifier = $security->getUser()->getUserIdentifier();
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
        $tournoi = new Tournoi();
        $form = $this->createForm(TournoiType::class, $tournoi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();
            // Process form 1 (update user information)
           $affiche =  $form->get('affiche')->getData();
           if ($affiche) {
            // Generate a unique name for the file
            $newFilename = uniqid().'.'.$affiche->guessExtension();
    
            // Move the file to the desired directory
            $affiche->move(
                $this->getParameter('image_directory'), // Path defined in services.yaml or config/packages/framework.yaml
                $newFilename
            );
            $formData->setAffiche($newFilename);
            $tournoi->setIdorganisateur($user);
        }
            $entityManager->persist($tournoi);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_tournoi', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionEvenement/tournoi/new.html.twig', [
            'tournoi' => $tournoi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tournoi_show', methods: ['GET'])]
    public function show(Tournoi $tournoi): Response
    {
        return $this->render('Back/GestionEvenement/tournoi/show.html.twig', [
            'tournoi' => $tournoi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tournoi_edit', methods: ['GET', 'POST'])]
    public function edit( Request $request, Tournoi $tournoi, EntityManagerInterface $entityManager): Response
    
    {
    
        $form = $this->createForm(TournoiType::class, $tournoi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_tournoi', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionEvenement/tournoi/edit.html.twig', [
            'tournoi' => $tournoi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tournoi_delete', methods: ['POST'])]
    public function delete(Request $request, Tournoi $tournoi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tournoi->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tournoi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_tournoi', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/tournoi/{id}", name="app_tournoi_detail")
     */
    public function detail(WeatherService $weatherService, Security $security, Request $request, $id, EntityManagerInterface $entityManager)
    {

        $city = $request->query->get('city');
    $forecast = null;

    // Si une ville est spécifiée, alors seulement faites l'appel au service météorologique.
    if ($city) {
        $forecast = $weatherService->getWeatherForecast($city);
    }

        
        $userIdentifier = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
        $tournoi = $this->getDoctrine()->getRepository(Tournoi::class)->find($id);
        
        $participation = new Participation();
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);
        $existingParticipation = $entityManager->getRepository(Participation::class)
        ->findOneBy(['idmembre' => $user, 'idtournoi' => $tournoi]);

        if ($form->isSubmitted() && $form->isValid()) {
            $participation->setIdmembre($user);
            $participation->setIdtournoi($tournoi);
            $entityManager->persist($participation);
            $entityManager->flush();
           
            return $this->redirectToRoute('app_Evenement', [], Response::HTTP_SEE_OTHER);
        }
        if (!$tournoi) {
            throw $this->createNotFoundException('Tournoi non trouvé');}

            
        return $this->render('Front/detaildutournoi.html.twig', [

            'tournoi' => $tournoi,
            'form' => $form->CreateView(),
            'participation' => $existingParticipation,
            'forecast' => $forecast,
            'city' => $city,
            'forecastAvailable' => $forecast !== null // Ajoutez cette ligne
           
        ]);
    }
}
