<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Reservation;
use App\Form\ReservationType;
// use App\Controller\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Terrain;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

use Mailtrap\Config;
use Mailtrap\Helper\ResponseHelper;
use Mailtrap\MailtrapSandboxClient;

use App\Entity\Blacklist;
use App\Controller\BlacklistController;

use App\Controller\PaymentController;
use Symfony\Component\Security\Core\Security;

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
    public function show(int $idreservation, EntityManagerInterface $entityManager): Response
    {
        $reservationRepository = $entityManager->getRepository(Reservation::class);

        $reservation = $reservationRepository->find($idreservation);

        if (!$reservation) {
            throw $this->createNotFoundException('Réservation non trouvée');
        }

        // Récupérer l'identifiant du terrain à partir de l'objet Reservation
        $idTerrain = $reservation->getIdterrain()->getId();

        // Appeler la méthode countUniqueReservationsByTerrain() avec l'identifiant du terrain
        $nb = $reservationRepository->countUniqueReservationsByTerrain($idTerrain);

        return $this->render('Back/GestionReservation/reservation/show.html.twig', [
            'reservation' => $reservation,
            'nbreReservation' => $nb,
        ]);
    }


    #[Route('/{idreservation}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $idreservation, EntityManagerInterface $entityManager): Response
    {
        $reservationRepository = $entityManager->getRepository(Reservation::class);

        // Récupérer la réservation à éditer à partir de la base de données
        $reservation = $reservationRepository->find($idreservation);

        if (!$reservation) {
            throw $this->createNotFoundException('Réservation non trouvée');
        }

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
    /**reserver terrain */
    #[Route('/getTerrain/{choix}/{idTerrain}/{date}/{horaire}', name: 'app_reservation_getTerrain', methods: ['POST'])]
    public function getTerrain(Security $security, Request $request, $choix, $idTerrain, $date, $horaire, EntityManagerInterface $entityManager, MailerInterface $mailer, PaymentController $paymentController): Response
    {
        $user = $security->getUser();
        if ($user == null) {
            return $this->redirectToRoute('app_login');
        } else {

            $userIdentifier = $security->getUser()->getUserIdentifier();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);


            $date = new \DateTime($date);
            $terrain = $entityManager->getRepository(Terrain::class)->find($idTerrain);

            if (!$terrain) {
                return new Response('Terrain non trouvé', Response::HTTP_NOT_FOUND);
            }

            $terrainDisponible = $entityManager->getRepository(Reservation::class)->findByDisponibility($terrain, $horaire, $date, $entityManager);

            // Retourner  JSON 
            if ($terrainDisponible) {



                $reservation = new Reservation();
                $reservation->setIsconfirm(false);
                $reservation->setDatereservation($date);
                $reservation->setHeurereservation($horaire);
                $reservation->setType($choix);
                $reservation->setIdterrain($terrain);

                $entityManager->persist($reservation);
                $entityManager->flush();
                // $this->sendEmail($mailer);


                // RECUPERE DERNIER RESERVATION

                $reservation2  = $entityManager->getRepository(Reservation::class)->findOneBy([], ['idreservation' => 'DESC']);
                if ($reservation2) {
                    $url = $paymentController->appelPaymentAPI($entityManager, $reservation2->getIdterrain()->getPrix(), $user->getId(), $reservation2);
                    return new Response($url, Response::HTTP_OK);
                }
            } else {
                return new Response('Terrain non disponible', 202);
            }
        }
    }








    #[Route('/reservations', name: 'get_reservations', methods: ['GET'])]
    public function getReservations(Security $security, ReservationRepository $reservationRepository): JsonResponse
    {
        $user = $security->getUser();
        if ($user == null) {
            return $this->redirectToRoute('app_login');
        } else {
            $reservations = $reservationRepository->findFutureAndUniqueReservations();


            $formattedReservations = [];
            foreach ($reservations as $reservation) {
                $formattedReservations[] = [
                    'id' => $reservation->getIdreservation(),
                    'datereservation' => $reservation->getDatereservation()->format('Y-m-d'),
                    'heurereservation' => $reservation->getHeurereservation(),
                    'idterrain' => [
                        'id' => $reservation->getIdterrain()->getId(),
                        'nom' => $reservation->getIdterrain()->getNomterrain(),
                        'adresse' => $reservation->getIdterrain()->getAddress(),
                        'prix' => $reservation->getIdterrain()->getPrix(),
                        'duree' => $reservation->getIdterrain()->getDuree()
                    ],
                ];
            }
            // Format JSON
            return new JsonResponse($formattedReservations);
        }
    }




    #[Route('/reservations/{idUser}', name: 'get_reservations', methods: ['GET'])]
    public function getFuturReservationsByIdUser($idUser, ReservationRepository $reservationRepository): JsonResponse
    {
        $FutureReservationsByUser = $reservationRepository->findFutureAndUniqueReservationsByIdUser($idUser);


        $formattedReservations = [];
        foreach ($FutureReservationsByUser as $reservation) {
            $formattedReservations[] = [
                'id' => $reservation->getIdreservation(),
                'datereservation' => $reservation->getDatereservation()->format('Y-m-d'),
                'heurereservation' => $reservation->getHeurereservation(),
                'idterrain' => [
                    'id' => $reservation->getIdterrain()->getId(),
                    'nom' => $reservation->getIdterrain()->getNomterrain(),
                    'adresse' => $reservation->getIdterrain()->getAddress(),
                    'prix' => $reservation->getIdterrain()->getPrix(),
                    'duree' => $reservation->getIdterrain()->getDuree()
                ],
            ];
        }
        // Format JSON
        return new JsonResponse($formattedReservations);
    }

    #[Route('/annulerReservation/{idReservation}', name: 'app_annuler_reservation', methods: ['GET', 'POST'])]
    public function annulerReservation(Security $security, $idReservation, Request $request, EntityManagerInterface $entityManager, BlacklistController $blacklistController): Response
    {


        $userIdentifier = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);


        $reservationRepository = $entityManager->getRepository(Reservation::class);
        $reservation = $reservationRepository->find($idReservation);
        if (!$reservation) {
            throw new \InvalidArgumentException('Réservation non trouvée.');
        }

        $currentTime = new \DateTime();
        $oneDayLater = (clone $currentTime)->add(new \DateInterval('P1D'));
        $difference = $oneDayLater->diff($reservation->getDatereservation());
        if ($difference->days === 0 && $difference->h < 24) {
            $user->setStatus(false);
            $reservation->setType('Compte_desactive');

            $entityManager->persist($user);
            $entityManager->persist($reservation);
            $entityManager->flush();

            $blacklistController->addToBlacklist($reservation, $entityManager);
        }

        $reservation->setHeurereservation('03:00');
        $reservation->setType('Annulation');
        $entityManager->persist($reservation);
        $entityManager->flush();



        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
