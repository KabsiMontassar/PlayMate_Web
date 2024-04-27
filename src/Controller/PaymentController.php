<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;

use DateTime;
use DateTimeZone;



use Symfony\Contracts\HttpClient\HttpClientInterface;

use App\Entity\Reservation;
use App\Entity\PaymentAPI;
use App\Entity\MailJettAPI;


use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[Route('/payment')]
class PaymentController extends AbstractController
{


    private $paymentAPI;
    private $httpClient;
    private $paiementService;
    private $mailJettAPI;


    public function __construct(PaymentAPI $paymentAPI, HttpClientInterface $httpClient)
    {
        $this->paymentAPI = $paymentAPI;
        $this->httpClient = $httpClient;
    }

    public function appelPaymentAPI(EntityManagerInterface $entityManage, float $prix, $membreId): string
    {
        try {
            $paiement = $this->creerPaiement($this->getDoctrine()->getManager(), $prix, $membreId);

            // Initialise le paiement
            $response = $this->paymentAPI->initPayment($paiement, $prix);


            if ($response != null) {
                // update 
                return $response['payUrl'];
            } else {
                // delete
                return "erreur";
            }
        } catch (TransportExceptionInterface $e) {
            echo "Erreur de requête HTTP";
            return "erreur";
        } catch (\Exception $e) {
            echo "Une erreur s'est produite : " . $e->getMessage();
            return "erreur";
        }
    }

    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test(): Response
    {
        $paiement = $this->creerPaiement($this->getDoctrine()->getManager(), 6000, 46);

        $response = $this->paymentAPI->initPayment($paiement, 10);
        return $this->json($response);
    }

    /**
     * 
     * 
     * *
     * **
     * **
     * *
     * *
     */
    #[Route('/payment-success', name: 'payment_success')]
    public function paymentSuccess(Request $request, EntityManagerInterface $entityManager): Response
    {
        /* a ajouter colonne base de donnee ref  */
        $paymentRef = $request->query->get('payment_ref');
        $payment = $entityManager->getRepository(Payment::class)->findLatestPayment();
        if ($payment) {
            $payment->setPaymentRef($paymentRef);
            $entityManager->persist($payment);
            $entityManager->flush();
            return $this->json(['message' => 'Payment successful']);
        } else {
            return $this->json(['error' => 'Payment not found']);
        }
    }


    /**
     * 
     * 
     * 
     */









    private function creerPaiement(EntityManagerInterface $entityManager, float $prix, int $membreId): Payment
    {
        // Obtenir la date et l'heure actuelles
        $dateCourante = new DateTime('now', new DateTimeZone('UTC'));

        // Formater la date et l'heure
        $dateString = $dateCourante->format('Y-m-d');
        $heureEnString = $dateCourante->format('H:i:s');

        $dateCourante2 = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
        // Créer un nouvel objet Paiement

        $user = $entityManager->getRepository(User::class)->find($membreId);
        if (!$user) {
            throw new \Exception("Membre non trouvé.");
        }
        $reservation = $entityManager->getRepository(Reservation::class)->find(40);
        if (!$reservation) {
            throw new \Exception("Reservation not found.");
        }

        $paiement = new Payment();
        $paiement->setIdmembre($user);
        $paiement->setIdreservation($reservation);
        $paiement->setDatepayment($dateCourante2);
        $paiement->setHorairepayment($heureEnString);
        //$paiement->setRef($idRef); // Remplacer par la valeur de votre champ
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($paiement);
        $entityManager->flush();
        return $paiement;
    }



    #[Route('/', name: 'app_payment_index', methods: ['GET'])]
    public function index(PaymentRepository $paymentRepository): Response
    {
        return $this->render('Back/GestionReservation/payment/payment.html.twig', [
            'payments' => $paymentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_payment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($payment);
            $entityManager->flush();

            return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionReservation/payment/new.html.twig', [
            'payment' => $payment,
            'form' => $form,
        ]);
    }

    #[Route('/{idpayment}', name: 'app_payment_show', methods: ['GET'])]
    public function show(int $idpayment, EntityManagerInterface $entityManager): Response
    {
        $paymentRepository = $entityManager->getRepository(Payment::class);

        $payment = $paymentRepository->find($idpayment);
        return $this->render('Back/GestionReservation/payment/show.html.twig', [
            'payment' => $payment,
        ]);
    }

    #[Route('/{idpayment}/edit', name: 'app_payment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $idpayment, EntityManagerInterface $entityManager): Response
    {
        $paymentRepository = $entityManager->getRepository(Payment::class);

        // Récupérer le paiement à éditer à partir de la base de données
        $payment = $paymentRepository->find($idpayment);

        if (!$payment) {
            throw $this->createNotFoundException('Paiement non trouvé');
        }

        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionReservation/payment/edit.html.twig', [
            'payment' => $payment,
            'form' => $form,
        ]);
    }


    #[Route('/{idpayment}', name: 'app_payment_delete', methods: ['POST'])]
    public function delete(Request $request, Payment $payment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $payment->getIdpayment(), $request->request->get('_token'))) {
            $entityManager->remove($payment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
    }
}
