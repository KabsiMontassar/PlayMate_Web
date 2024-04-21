<?php

namespace App\Controller;

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


use App\Entity\PaymentAPI;
use App\Entity\MailJettAPI;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[Route('/payment')]
class PaymentController extends AbstractController
{


    private $paymentAPI;
    private $httpClient;
    private $paiementService;
    private $mailJettAPI;


    public function __construct(PaymentAPI $paymentAPI, HttpClientInterface $httpClient,  MailJettAPI $mailJettAPI)
    {
        $this->paymentAPI = $paymentAPI;
        $this->httpClient = $httpClient;

        $this->mailJettAPI = $mailJettAPI;
    }

    public function appelPaymentAPI(int $prix): void
    {
        try {
            $paiement = $this->creerPaiement();

            // Initialise le paiement
            $response = $this->paymentAPI->initPayment($paiement, $prix);

            // Récupère l'URL de paiement
            $payUrl = $this->paymentAPI->extractPayUrlFromResponse($response);

            if (!empty($payUrl)) {
                // Ouvre l'URL de paiement dans le navigateur
                $browser = new HttpBrowser();
                $browser->get($payUrl);

                // Attendre un certain temps pour que l'utilisateur effectue le paiement
                sleep(60); // Attend 1 min (par exemple)

                // Vérifie si le paiement est réussi en utilisant la référence de paiement
                $paymentSuccessful = $this->paymentAPI->isPaymentSuccessful();

                if ($paymentSuccessful) {
                    // Récupère la référence de paiement
                    $paymentRef = $this->paymentAPI->getPaymentRef();

                    // Ajoute le paiement avec sa référence
                    $this->paiementService->AjouterPaiement($paiement, $paymentRef);

                    // Envoie un email de confirmation
                    $this->mailJettAPI->send('aziztaraji1@gmail.com', 'playmatepidev@gmail.com', $paymentRef);
                } else {
                    echo "Erreur de paiement";
                }
            }
        } catch (TransportExceptionInterface $e) {
            echo "Erreur de requête HTTP";
        } catch (\Exception $e) {
            echo "Une erreur s'est produite : " . $e->getMessage();
        }
    }

    private function creerPaiement(): Payment
    {
        // Obtenir la date et l'heure actuelles
        $dateCourante = new DateTime('now', new DateTimeZone('UTC'));

        // Formater la date et l'heure
        $dateString = $dateCourante->format('Y-m-d');
        $heureEnString = $dateCourante->format('H:i:s');

        $dateCourante2 = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
        // Créer un nouvel objet Paiement
        $paiement = new Payment();
        $paiement->setIdmembre(/* Remplacer par l'ID de l'utilisateur */30);
        $paiement->setIdReservation(/* Remplacer par l'ID de la réservation */8);
        $paiement->setDatepayment($dateCourante2);
        $paiement->setHorairepayment($heureEnString);
        //$paiement->setRef(''); // Remplacer par la valeur de votre champ

        return $paiement;
    }

    public function ajouterPaiement(Payment $paiement, string $paymentRef): bool
    {
        $entityManager = $this->getDoctrine()->getManager();

        // Créez une nouvelle instance de Paiement
        $newPayment = new Payment();
        $newPayment->setIdMembre($paiement->getIdMembre());
        $newPayment->setIdReservation($paiement->getIdReservation());
        $newPayment->setDatePayment($paiement->getDatePayment());
        $newPayment->setHorairePayment($paiement->getHorairePayment());
        //$newPayment->setPaymentRef($paymentRef);

        // Persistez et flush pour sauvegarder les modifications dans la base de données
        $entityManager->persist($newPayment);
        $entityManager->flush();

        // Vérifiez si une ligne a été affectée
        return $newPayment->getIdpayment() !== null;
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
