<?php

namespace App\Controller;

use App\Entity\Membreparequipe;
use App\Form\MembreparequipeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
