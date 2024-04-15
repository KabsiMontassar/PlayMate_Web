<?php

namespace App\Controller;

use App\Entity\Terrain;
use App\Entity\Avis;
use App\Entity\User;
use App\Form\TerrainType;
use App\Form\AvisType;
use App\Repository\TerrainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
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

    #[Route('/profile', name: 'app_user_terrain')]
public function userTerrain(Security $security, EntityManagerInterface $entityManager): Response
{
    $userIdentifier = $security->getUser()->getUserIdentifier();
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);

    $terrains = $entityManager->getRepository(Terrain::class)->findBy(['idprop' => $user]);
    
    // Render the template with the tournaments
    return $this->render('Back/Terrains/terrain/profileterrain.html.twig', [
        'terrains' => $terrains,
    ]);
}

 //********************************************************************************************

    #[Route('/new', name: 'app_terrain_new', methods: ['GET', 'POST'])]
    public function new(Security $security, Request $request, EntityManagerInterface $entityManager): Response
    {
    $userIdentifier = $security->getUser()->getUserIdentifier();
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
        $terrain = new Terrain();
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);
    
        if ($form->isSubmitted()) {
            // Récupérer le nom, le gouvernorat et le prix du formulaire
            $nomTerrain = $form->get('nomterrain')->getData();
            $gouvernorat = $form->get('gouvernorat')->getData();
            $prix = $form->get('prix')->getData();
    
            // Vérifier si le nom et le gouvernorat sont des lettres majuscules et minuscules
            if (!preg_match('/^[a-zA-Z]+$/', $nomTerrain) || !preg_match('/^[a-zA-Z]+$/', $gouvernorat)) {
                // Gérer l'erreur, par exemple afficher un message à l'utilisateur
                // Rediriger vers le formulaire avec un message d'erreur
                return $this->render('Back/Terrains/terrain/new.html.twig', [
                    'terrain' => $terrain,
                    'form' => $form->createView(),
                    'error_message' => 'Le nom et le gouvernorat doivent contenir uniquement des lettres.'
                ]);
            }
    
            // Vérifier si le prix est un entier
            if (!is_numeric($prix)) {
                // Gérer l'erreur, par exemple afficher un message à l'utilisateur
                // Rediriger vers le formulaire avec un message d'erreur
                return $this->render('Back/Terrains/terrain/new.html.twig', [
                    'terrain' => $terrain,
                    'form' => $form->createView(),
                    'error_message' => 'Le prix doit être un entier.'
                ]);
            }
    
            // Continuer le traitement si tout est valide
            $terrain->setNomterrain($nomTerrain);
            $terrain->setGouvernorat($gouvernorat);
            $terrain->setIdprop($user);
            $terrain->setPrix($prix);
    
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
    
            return $this->redirectToRoute('app_user_terrain', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('Back/Terrains/terrain/new.html.twig', [
            'terrain' => $terrain,
            'form' => $form->createView(),
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

//********************************************************************************************

#[Route('/{id}', name: 'app_terrain_show', methods: ['GET'])]
public function show(Terrain $terrain): Response
{
    return $this->render('Back/Terrains/terrain/show.html.twig', [
        'terrain' => $terrain,
    ]);}}