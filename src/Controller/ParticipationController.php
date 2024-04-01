<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Entity\User;
use App\Form\ParticipationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\UserController;
use App\Repository\UserRepository;


#[Route('/participation')]
class ParticipationController extends AbstractController
{
    #[Route('/', name: 'app_participation_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        
        $user = $entityManager->getRepository(User::class)->find(43);

        $participations = $entityManager
            ->getRepository(Participation::class)
            ->findAll();

        return $this->render('Back/GestionEvenement/participation/participation.html.twig', [
            'participations' => $participations,
            'user' => $user,
        ]);
    }

    #[Route('/new/{iduser}', name: 'app_participation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ?int $iduser): Response
    {
        $user = $entityManager->getRepository(User::class)->find($iduser);
        $participation = new Participation();
        $form = $this->createForm(ParticipationType::class, $participation, ['user' => $user]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $formdata=$form->getData();
            $formdata->setIdmembre($user);
            $entityManager->persist($formdata);
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER,);
        }

        return $this->renderForm('Back/GestionEvenement/participation/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/{iduser}', name: 'app_participation_show', methods: ['GET'])]
    public function show(Participation $participation,  int $iduser,EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($iduser);

        return $this->render('Back/GestionEvenement/participation/show.html.twig', [
            'participation' => $participation,
            'user' => $user,
        ]);
    }

   /* #[Route('/{id}/edit', name: 'app_participation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionEvenement/participation/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }*/

    #[Route('/{id}/{iduser}', name: 'app_participation_delete', methods: ['POST'])]
    public function delete(int $iduser,Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($iduser);

        if ($this->isCsrfTokenValid('delete'.$participation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participation_index', [ 'user' => $user], Response::HTTP_SEE_OTHER);
    }
}
