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


use App\Entity\Reservation;

#[Route('/blacklist')]
class BlacklistController extends AbstractController
{
    #[Route('/', name: 'app_blacklist_index', methods: ['GET'])]
    public function index(BlacklistRepository $blacklistRepository): Response
    {
        return $this->render('Back/GestionReservation/blacklist/blacklist.html.twig', [
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

        return $this->renderForm('Back/GestionReservation/blacklist/new.html.twig', [
            'blacklist' => $blacklist,
            'form' => $form,
        ]);
    }

    #[Route('/{idblacklist}', name: 'app_blacklist_show', methods: ['GET'])]
    public function show(int $idblacklist, EntityManagerInterface $entityManager): Response
    {
        $blacklistRepository = $entityManager->getRepository(Blacklist::class);

        // Récupérer l'entité Blacklist à afficher à partir de la base de données
        $blacklist = $blacklistRepository->find($idblacklist);

        if (!$blacklist) {
            throw $this->createNotFoundException('Blacklist non trouvée');
        }

        return $this->render('Back/GestionReservation/blacklist/show.html.twig', [
            'blacklist' => $blacklist,
        ]);
    }

    #[Route('/{idblacklist}/edit', name: 'app_blacklist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $idblacklist, EntityManagerInterface $entityManager): Response
    {
        $blacklistRepository = $entityManager->getRepository(Blacklist::class);

        // Récupérer l'entité Blacklist à éditer à partir de la base de données
        $blacklist = $blacklistRepository->find($idblacklist);

        if (!$blacklist) {
            throw $this->createNotFoundException('Blacklist non trouvée');
        }

        $form = $this->createForm(BlacklistType::class, $blacklist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_blacklist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionReservation/blacklist/edit.html.twig', [
            'blacklist' => $blacklist,
            'form' => $form,
        ]);
    }

    #[Route('/{idblacklist}', name: 'app_blacklist_delete', methods: ['POST'])]
    public function delete(Request $request, int $idblacklist, EntityManagerInterface $entityManager): Response
    {
        $blacklistRepository = $entityManager->getRepository(Blacklist::class);

        // Récupérer l'entité Blacklist à supprimer à partir de la base de données
        $blacklist = $blacklistRepository->find($idblacklist);

        if (!$blacklist) {
            throw $this->createNotFoundException('Blacklist non trouvée');
        }

        if ($this->isCsrfTokenValid('delete' . $blacklist->getIdblacklist(), $request->request->get('_token'))) {
            $entityManager->remove($blacklist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_blacklist_index', [], Response::HTTP_SEE_OTHER);
    }



    public function addToBlacklist(Reservation $reservation, EntityManagerInterface $entityManager): void
    {

        $blacklist = new Blacklist();
        $blacklist->setIdreservation($reservation);
        $blacklist->setDuree(30);
        $blacklist->setCause('Annulation de la réservation');



        $entityManager->persist($blacklist);
        $entityManager->flush();
    }
}
