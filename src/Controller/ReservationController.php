<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Controller\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Terrain;

use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('Back/GestionReservation/reservation/Reservation.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionReservation/reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{idreservation}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('Back/GestionReservation/reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{idreservation}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionReservation/reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{idreservation}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getIdreservation(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/getTerrain/{choix}/{idTerrain}/{date}/{horaire}', name: 'app_reservation_getTerrain', methods: ['POST'])]
    public function getTerrain(Request $request, $choix, $idTerrain, $date, $horaire, EntityManagerInterface $entityManager): Response
    {
        
        // convert date to DateTimeInterface 
        $date = new \DateTime($date);
        // idterrain must be of type ?App\Entity\Terrain
        $idTerrain = $entityManager->getRepository(Terrain::class)->find($idTerrain);
        // Effectuer la logique de vérification de disponibilité du terrain
        $terrainDisponible = $entityManager->getRepository(Reservation::class)->findByDisponibility($idTerrain, $horaire, $date, $entityManager);

        // Retourner une réponse JSON en fonction du résultat de la vérification
        if ($terrainDisponible) {
            $reservation = new Reservation();
            $reservation->setIsconfirm(false);
            $reservation->setDatereservation($date);
            $reservation->setHeurereservation($horaire);
            $reservation->setType($choix);
            $reservation->setIdterrain($idTerrain);

            $entityManager->persist($reservation);
            $entityManager->flush();

            return new Response('Terrain disponible', Response::HTTP_OK);
        } else {
            return new Response('Terrain non disponible', Response::HTTP_OK);
        }
    }
}
