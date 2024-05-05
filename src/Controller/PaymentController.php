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


use App\Repository\ReservationRepository;


use App\Entity\Terrain;

use Knp\Component\Pager\PaginatorInterface;


use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

use Mailtrap\Config;
use Mailtrap\Helper\ResponseHelper;
use Mailtrap\MailtrapSandboxClient;

use App\Entity\Blacklist;
use App\Controller\BlacklistController;


use Symfony\Component\Security\Core\Security;

#[Route('/payment')]
class PaymentController extends AbstractController
{


    private $paymentAPI;
    private $httpClient;
    private $paiementService;
    private $mailJettAPI;
    private $mailer;

    public function __construct(PaymentAPI $paymentAPI, HttpClientInterface $httpClient, MailerInterface $mailer)
    {
        $this->paymentAPI = $paymentAPI;
        $this->httpClient = $httpClient;
        $this->mailer = $mailer;
    }




    public function appelPaymentAPI(EntityManagerInterface $entityManage,  $prix, $membreId,  $idReservation): string
    {
        try {
            $paiement = $this->creerPaiement($this->getDoctrine()->getManager(), $prix, $membreId, $idReservation);

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
    /*
    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test(): Response
    {
        $paiement = $this->creerPaiement($this->getDoctrine()->getManager(), 6000, 46);

        $response = $this->paymentAPI->initPayment($paiement, 10);
        return $this->json($response);
    }
*/

    #[Route('/payment-success', name: 'payment_success')]
    public function paymentSuccess(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {

        $paymentRef = $request->query->get('payment_ref');
        $payment = $entityManager->createQueryBuilder()
            ->select('p')
            ->from(Payment::class, 'p')
            ->orderBy('p.idpayment', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if ($payment) {
            $payment->setPaymentRef($paymentRef);
            $payment->setPayed(true);
            $entityManager->persist($payment);
            $entityManager->flush();
            $this->sendEmail($mailer, $payment);
            return $this->json(['message' => 'Payment successful']);
        } else {
            return $this->json(['error' => 'Payment not found']);
        }
    }


    public function sendEmail(MailerInterface $mailer, $payment)
    {

        $htmlTemplate = $this->renderView('mailer/mailConfirmationReservation.html.twig', [
            'payment' => $payment,
        ]);
        $apikey = '6775274d71a8a7c5aa766d4fc491bdcf';
        $mailtrap = new MailtrapSandboxClient(new Config($apikey));
        $email = (new Email())
            ->from('no-replay@PlayMate.com')
            ->to($payment->getIdmembre()->getEmail())
            ->subject('confirmation de reservation!')
            ->text('Sending emails is fun again!')
            ->html($htmlTemplate);


        $response = $mailtrap->emails()->send($email, '2815840'); // Email sending API (real)

        var_dump(ResponseHelper::toArray($response)); // body (array)
    }

    /**
     * 
     * 
     * 
     */









    public function creerPaiement(EntityManagerInterface $entityManager, float $prix, int $membreId,  $idReservation): Payment
    {
        $dateCourante = new DateTime('now', new DateTimeZone('UTC'));

        $dateString = $dateCourante->format('Y-m-d');
        $heureEnString = $dateCourante->format('H:i:s');

        $dateCourante2 = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));

        $user = $entityManager->getRepository(User::class)->find($membreId);
        if (!$user) {
            throw new \Exception("Membre non trouvé.");
        }
        $reservation = $entityManager->getRepository(Reservation::class)->find($idReservation);
        if (!$reservation) {
            throw new \Exception("Reservation not found.");
        }

        $paiement = new Payment();
        $paiement->setIdmembre($user);
        $paiement->setIdreservation($reservation);
        $paiement->setDatepayment($dateCourante2);
        $paiement->setHorairepayment($heureEnString);
        $paiement->setPayed(false);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($paiement);
        $entityManager->flush();
        return $paiement;
    }



    #[Route('/', name: 'app_payment_index', methods: ['GET'])]
    public function index(Request $request, PaymentRepository $paymentRepository, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $paymentRepository->createQueryBuilder('p');

        $paiment = $paymentRepository->findAll();
        $paiment = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('Back/GestionReservation/payment/payment.html.twig', [
            'payments' => $paiment,
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
