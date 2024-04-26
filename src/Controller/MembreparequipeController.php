<?php

namespace App\Controller;

use App\Entity\Membreparequipe;
use App\Form\MembreparequipeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Equipe;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

#[Route('/membreparequipe')]
class MembreparequipeController extends AbstractController
{
    #[Route('/', name: 'app_membreparequipe_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $membreparequipes = $entityManager
            ->getRepository(Membreparequipe::class)
            ->findAll();

        return $this->render('Back/GestionEquipe/MembreParEquipe/MembreParEquipe.html.twig', [
            'membreparequipes' => $membreparequipes,
        ]);
    }

    #[Route('/new', name: 'app_membreparequipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $membreparequipe = new Membreparequipe();
        $form = $this->createForm(MembreparequipeType::class, $membreparequipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($membreparequipe);
            $entityManager->flush();

            return $this->redirectToRoute('app_membreparequipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionEquipe/MembreParEquipe/new.html.twig', [
            'membreparequipe' => $membreparequipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_membreparequipe_show', methods: ['GET'])]
    public function show(Membreparequipe $membreparequipe): Response
    {
        return $this->render('Back/GestionEquipe/MembreParEquipe/show.html.twig', [
            'membreparequipe' => $membreparequipe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_membreparequipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Membreparequipe $membreparequipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MembreparequipeType::class, $membreparequipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_membreparequipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionEquipe/MembreParEquipe/edit.html.twig', [
            'membreparequipe' => $membreparequipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_membreparequipe_delete', methods: ['POST'])]
    public function delete(Request $request, Membreparequipe $membreparequipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$membreparequipe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($membreparequipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_membreparequipe_index', [], Response::HTTP_SEE_OTHER);
    }

   


    #[Route('/{teamName}/{memberId}', name: 'app_delete_team_member', methods: ['GET','POST'])]
    public function deletemembre(Request $request, EntityManagerInterface $entityManager , Security $security): Response
    {
        $memberId = $request->get('memberId');
        $teamName = $request->get('teamName');
        $userIdentifier = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
       // get the equipid from the teamName 
       $equipe = $entityManager
       ->getRepository(Equipe::class)
       ->findOneBy(['nomequipe' => $teamName]);


   $membreparequipe = $entityManager
       ->getRepository(Membreparequipe::class)
       ->findOneBy(['idequipe' => $equipe, 'idmembre' => $memberId]);
   
        // nombre of membre in the team 
        $numberofparticipant = $entityManager->getRepository(Membreparequipe::class)->findBy(['idequipe' => $equipe]);
        $number = count($numberofparticipant);
        
        if( $user->getId() == $memberId && $number == 1){
            // delete the equipe 
            $equipe = $entityManager->getRepository(Equipe::class)->findOneBy(['nomequipe' => $teamName]);
            $entityManager->remove($equipe);
            $entityManager->flush();
            return $this->redirectToRoute('First', [], Response::HTTP_SEE_OTHER);
        }
     
 
        if ($membreparequipe) {
            $entityManager->remove($membreparequipe);
            $entityManager->flush();
        }



        return $this->redirectToRoute('First', [], Response::HTTP_SEE_OTHER);
    }

   
    #[Route('/add/{teamName}/{emailtoadd}', name: 'app_add_team_member', methods: ['GET','POST'])]
    public function addmembre(Request $request, EntityManagerInterface $entityManager , Security $security, string $teamName, string $emailtoadd): Response
    {
        $userIdentifier = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
    
        $usertoadd = $entityManager->getRepository(User::class)->findOneBy(['email' => $emailtoadd]);
        $equipe = $entityManager->getRepository(Equipe::class)->findOneBy(['nomequipe' => $teamName]);
    
        if($user->getId() == $usertoadd->getId()){
            return new Response('Failed', Response::HTTP_OK);
        }
        $numberofparticipant = $entityManager->getRepository(Membreparequipe::class)->findBy(['idequipe' => $equipe]);
        $number = count($numberofparticipant);
        if($equipe->getNbrejoueur() == $number ){
            return new Response('Failed', Response::HTTP_OK);
        }
    
        $membreparequipe = new Membreparequipe();
        $membreparequipe->setIdmembre($usertoadd);
        $membreparequipe->setIdequipe($equipe);
        $entityManager->persist($membreparequipe);
        $entityManager->flush();
    
        return new Response('Success', Response::HTTP_OK);
    }
    
}