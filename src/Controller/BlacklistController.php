<?php

namespace App\Controller;

use App\Entity\Blacklist;
use App\Form\BlacklistType;
use App\Repository\BlacklistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/blacklist')]
class BlacklistController extends AbstractController
{
    #[Route('/', name: 'app_blacklist_index', methods: ['GET'])]
    public function index(BlacklistRepository $blacklistRepository): Response
    {
        return $this->render('blacklist/index.html.twig', [
            'blacklists' => $blacklistRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_blacklist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blacklist = new Blacklist();
        $form = $this->createForm(BlacklistType::class, $blacklist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($blacklist);
            $entityManager->flush();

            return $this->redirectToRoute('app_blacklist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blacklist/new.html.twig', [
            'blacklist' => $blacklist,
            'form' => $form,
        ]);
    }

    #[Route('/{idblacklist}', name: 'app_blacklist_show', methods: ['GET'])]
    public function show(Blacklist $blacklist): Response
    {
        return $this->render('blacklist/show.html.twig', [
            'blacklist' => $blacklist,
        ]);
    }

    #[Route('/{idblacklist}/edit', name: 'app_blacklist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blacklist $blacklist, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BlacklistType::class, $blacklist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_blacklist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blacklist/edit.html.twig', [
            'blacklist' => $blacklist,
            'form' => $form,
        ]);
    }

    #[Route('/{idblacklist}', name: 'app_blacklist_delete', methods: ['POST'])]
    public function delete(Request $request, Blacklist $blacklist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blacklist->getIdblacklist(), $request->request->get('_token'))) {
            $entityManager->remove($blacklist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_blacklist_index', [], Response::HTTP_SEE_OTHER);
    }
}
