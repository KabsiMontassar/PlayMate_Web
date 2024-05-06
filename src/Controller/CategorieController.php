<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie')]
class CategorieController extends AbstractController
{
    #[Route('/', name: 'app_categorie_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('Back/GestionProduit/categorie/categorie.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'app_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $nom = $form->get('nom')->getData();
            $description = $form->get('description')->getData();
            if (!preg_match('/^[a-zA-Z]+$/', $nom) || !preg_match('/^[a-zA-Z]+$/', $description)) {
                // Gérer l'erreur, par exemple afficher un message à l'utilisateur
                // Rediriger vers le formulaire avec un message d'erreur
                return $this->render('Back/GestionProduit/Produit/new.html.twig', [
                    'categorie' => $categorie,
                    'form' => $form->createView(),
                    'error_message' => 'Le nom et le gouvernorat doivent contenir uniquement des lettres.'
                ]);
            }
            $categorie->setNom($nom);
            $categorie->setDescription($description);
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionProduit/categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }
   

    #[Route('/{id}', name: 'app_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager, int $id): Response
    {
        $categorie = $entityManager->getRepository(categorie::class)->find($id);
        if (!$categorie) {
                throw $this->createNotFoundException('Produit non trouvé');
            }
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionProduit/categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager, int $id): Response
    {   
        $categorie = $entityManager->getRepository(categorie::class)->find($id); 
        if (!$categorie) {
                throw $this->createNotFoundException('categorie non trouvé');
            } 
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
