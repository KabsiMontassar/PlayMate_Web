<?php

namespace App\Controller;

use App\Entity\Terrain;
use App\Entity\Avis;
use App\Form\TerrainType;
use App\Form\AvisType;
use App\Repository\TerrainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//********************************************************************************************

#[Route('/terrain')]
class TerrainController extends AbstractController
{
    #[Route('/', name: 'app_terrain_index', methods: ['GET'])]
    public function index(TerrainRepository $terrainRepository): Response
    {
        // Récupérer les terrains avec le statut "disponible"
        $terrainsDisponibles = $terrainRepository->findBy(['status' => true]);
        return $this->render('Back/Terrains/terrain/index.html.twig', [
            'terrains' => $terrainRepository->findAll(),]);
    }

    //********************************************************************************************

    #[Route('/new', name: 'app_terrain_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
             $terrain = new Terrain();
             $form = $this->createForm(TerrainType::class, $terrain);
             $form->handleRequest($request);
    if ($form->isSubmitted()) {
             $formdata = $form->getdata();

       // Vérifier si un fichier image a été fourni
            $imageFile = $form['image']->getData();
    if ($imageFile) {
       // Gérer l'upload de l'image
            $newFilename = uniqid().'.'.$imageFile->guessExtension();
            $imageFile->move(
        $this->getParameter('terrain_images_directory'),
            $newFilename);
            $terrain->setImage($newFilename);
}

        // Vérifier si un fichier vidéo a été fourni
            $videoFile = $form['video']->getData();
    if ($videoFile) {
    // Gérer l'upload de la vidéo
            $newFilename = uniqid().'.'.$videoFile->guessExtension();
            $videoFile->move(
        $this->getParameter('terrain_videos_directory'),
            $newFilename
    );
            $terrain->setVideo($newFilename);
}

            $entityManager->persist($terrain);
            $entityManager->flush();
        return $this->redirectToRoute('app_terrain_index', [], Response::HTTP_SEE_OTHER);
    }
        return $this->renderForm('Back/Terrains/terrain/new.html.twig', [
        'terrain' => $terrain,
        'form' => $form,
    ]);
}

//********************************************************************************************

    #[Route('/{id}', name: 'app_terrain_show', methods: ['GET'])]
    public function show(Terrain $terrain): Response
    {
        return $this->render('Back/Terrains/terrain/show.html.twig', [
            'terrain' => $terrain,
        ]);
    }
     
    //********************************************************************************************

    #[Route('/{id}/edit', name: 'app_terrain_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
            $terrain = $entityManager->getRepository(Terrain::class)->find($id);
    if (!$terrain) {
            throw $this->createNotFoundException('Terrain non trouvé');
        }
            $form = $this->createForm(TerrainType::class, $terrain);
            $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de l'image
            $imageFile = $form['image']->getData();
    if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('terrain_images_directory'),
                $newFilename
                );
                $terrain->setImage($newFilename);
            }
            // Gérer l'upload de la vidéo
            $videoFile = $form['video']->getData();
    if ($videoFile) {
                $newFilename = uniqid().'.'.$videoFile->guessExtension();
                $videoFile->move(
                    $this->getParameter('terrain_videos_directory'),
                    $newFilename
                );
                $terrain->setVideo($newFilename);
            }
                $entityManager->flush();
        return $this->redirectToRoute('app_terrain_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('Back/Terrains/terrain/edit.html.twig', [
            'terrain' => $terrain,
            'form' => $form->createView(),
        ]);
    }
    
//********************************************************************************************

    #[Route('/{id}', name: 'app_terrain_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
            $terrain = $entityManager->getRepository(Terrain::class)->find($id); 
    if (!$terrain) {
            throw $this->createNotFoundException('Terrain non trouvé');
        } 
    if ($this->isCsrfTokenValid('delete'.$terrain->getId(), $request->request->get('_token'))) {
            $entityManager->remove($terrain);
            $entityManager->flush();
        } 
        return $this->redirectToRoute('app_terrain_index', [], Response::HTTP_SEE_OTHER);
    }

    //********************************************************************************************

 /**
     * @Route("/terrain/{id}", name="app_terrain_detail")
     */
    public function detail($id)
    {
        // Récupérer les détails du terrain en fonction de $id (par ex. depuis la base de données)
        $terrain = $this->getDoctrine()->getRepository(Terrain::class)->find($id);

        // Vérifier si le terrain existe
        if (!$terrain) {
            throw $this->createNotFoundException('Terrain non trouvé');}

        // Vérifier si le terrain existe et s'il est disponible
         if (!$terrain || !$terrain->isStatus()) {
             throw $this->createNotFoundException('Le terrain n\'existe pas ou n\'est pas disponible.');
}
        // Passer les détails du terrain au template
        return $this->render('Front/detailt.html.twig', [
            'terrain' => $terrain // Passer le terrain récupéré au template
        ]);
    }

        //********************************************************************************************

/**
     * @Route("/terrain/{id}/donner-avis", name="app_donner_avis")
     */
    public function donnerAvis($id, Request $request)
{
        $entityManager = $this->getDoctrine()->getManager();
        $terrain = $entityManager->getRepository(Terrain::class)->find($id);

if (!$terrain) {
        throw $this->createNotFoundException('Terrain non trouvé');
    }
        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
        $avis = $form->getData();
        $avis->setTerrain($terrain); 
        $entityManager->persist($avis);
        $entityManager->flush(); 
        // Redirection vers la page de détails du terrain
        return $this->redirectToRoute('app_terrain_detail', ['id' => $id]);
    } 
    return $this->render('Front/donner_avis.html.twig', [
        'form' => $form->createView(),
        'terrain' => $terrain,
    ]);
} 
}