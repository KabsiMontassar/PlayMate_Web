<?php



namespace App\Controller;


use App\Entity\Historique;
use App\Entity\PdfService;
use App\Form\HistoriqueType;
use App\Repository\HistoriqueRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/historique')]
class HistoriqueController extends AbstractController
{
    #[Route('/', name: 'app_historique_index', methods: ['GET'])]
    public function index(HistoriqueRepository $historiqueRepository, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager): Response
    {

        $currentDate = new \DateTime();
        $reservations = $reservationRepository->findPreviousReservations($currentDate);

        foreach ($reservations as $reservation) {
            $existingHistorique = $historiqueRepository->findOneBy(['reservation' => $reservation]);
            if (!$existingHistorique) {
                $historique = new Historique();

                $historique->setReservation($reservation);

                $entityManager->persist($historique);
            }
        }
        $entityManager->flush();

        $historiques = $historiqueRepository->findAll();
        return $this->render('Back/GestionReservation/historique/historique.html.twig', [
            'historiques' => $historiques,
        ]);
    }

    #[Route('/new', name: 'app_historique_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $historique = new Historique();
        $form = $this->createForm(HistoriqueType::class, $historique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($historique);
            $entityManager->flush();

            return $this->redirectToRoute('app_historique_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionReservation/historique/new.html.twig', [
            'historique' => $historique,
            'form' => $form,
        ]);
    }

    #[Route('/{idhistorique}', name: 'app_historique_show', methods: ['GET'])]
    public function show(Historique $historique): Response
    {
        return $this->render('Back/GestionReservation/historique/show.html.twig', [
            'historique' => $historique,
        ]);
    }

    #[Route('/{idhistorique}/edit', name: 'app_historique_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Historique $historique, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HistoriqueType::class, $historique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_historique_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionReservation/historique/edit.html.twig', [
            'historique' => $historique,
            'form' => $form,
        ]);
    }

    #[Route('/{idhistorique}', name: 'app_historique_delete', methods: ['POST'])]
    public function delete(Request $request, Historique $historique, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $historique->getIdhistorique(), $request->request->get('_token'))) {
            $entityManager->remove($historique);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_historique_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/pdf/{date}/{horaire}/{nomterrain}/{adresse}/{prix}', name: 'personne.pdf')]
    public function generatePdfPersonne($date, $horaire, $nomterrain, $adresse, $prix, PdfService $pdf) //: Response
    { // Render the PDF with all necessary data

        $data = [
            'date' => $date,
            'horaire' => $horaire,
            'nomterrain' => $nomterrain,
            'adresse' => $adresse,
            'prix' => $prix
        ];

        $html = $this->renderView('Front/detailPdf.html.twig', $data);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');

        // Instantiate Dompdf with the configured options
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Generate PDF file name
        $pdfFileName = 'reservation.pdf';

        // Build the path for PDF storage
        $pdfPath = $this->getParameter('kernel.project_dir') . '/public/pdf/' . $pdfFileName;

        // Ensure the directory exists
        $pdfDir = dirname($pdfPath);
        if (!file_exists($pdfDir)) {
            mkdir($pdfDir, 0777, true);
        }

        // Save the generated PDF
        file_put_contents($pdfPath, $dompdf->output());

        // Output the generated PDF to the browser (inline download)
        return new BinaryFileResponse($pdfPath);
    }

    /*
    #[Route('/pdf/{date}/{horaire}/{nomterrain}/{adresse}/{prix}', name: 'personne.pdf')]
    public function generatePdfPersonne($date, $horaire, $nomterrain, $adresse, $prix, PdfService $pdf) //: Response
    {
        $data = [
            'date' => $date,
            'horaire' => $horaire,
            'nomterrain' => $nomterrain,
            'adresse' => $adresse,
            'prix' => $prix
        ];

        // Rendre la vue Twig et récupérer le contenu HTML
        $htmlContent = $this->renderView('Front/detailPdf.html.twig', $data);

        // Rendre la vue Twig pour le bloc title et récupérer son contenu
        $titleBlock = $this->renderView('Front/detailPdf.html.twig', $data, ['block_name' => 'title']);

        // Ajouter le contenu du bloc title au contenu HTML
        $htmlContent .= $titleBlock;

        // Générer le PDF avec Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($htmlContent);
        $dompdf->render();

        // Récupérer le contenu du PDF généré
        $pdfOutput = $dompdf->output();

        // Retourner une réponse avec le contenu PDF
        return new Response(
            $pdfOutput,
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );

        //$html = $this->render('Front/detailPdf.html.twig', ['date' => $date, 'horaire' => $horaire, 'nomterrain' => $nomterrain, 'adresse' => $adresse, 'prix' => $prix]);
        //$pdf->showPdfFile($html);
    }*/
}
