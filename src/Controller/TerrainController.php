<?php

namespace App\Controller;

use App\Entity\Terrain;
use App\Form\TerrainType;
use App\Repository\TerrainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/terrain')]
class TerrainController extends AbstractController
{
    #[Route('/', name: 'app_terrain_index', methods: ['GET'])]
    public function index(TerrainRepository $terrainRepository): Response
    {
        return $this->render('Back/Terrains/terrain/index.html.twig', [
            'terrains' => $terrainRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_terrain_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $terrain = new Terrain();
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

        $entityManager->persist($terrain);
        $entityManager->flush();

        return $this->redirectToRoute('app_terrain_index', [], Response::HTTP_SEE_OTHER);
    }
// get durre terrain from terain by id 
 
    return $this->renderForm('Back/Terrains/terrain/new.html.twig', [
        'terrain' => $terrain,
        'form' => $form,
    ]);
}
    #[Route('/{id}', name: 'app_terrain_show', methods: ['GET'])]
    public function show(Terrain $terrain): Response
    {


        // get durre terrain from terain by id
        return $this->render('Back/Terrains/terrain/show.html.twig', [
            'terrain' => $terrain,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_terrain_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
    {
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
    
        // Pré-remplir les champs image et vidéo avec leurs valeurs existantes
        $terrain->setImage($terrain->getImage());
        $terrain->setVideo($terrain->getVideo());
    
        return $this->render('Back/Terrains/terrain/edit.html.twig', [
            'terrain' => $terrain,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_terrain_delete', methods: ['POST'])]
    public function delete(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
    {
        
        if ($this->isCsrfTokenValid('delete'.$terrain->getId(), $request->request->get('_token'))) {
            $entityManager->remove($terrain);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_terrain_index', [], Response::HTTP_SEE_OTHER);
    }
}
