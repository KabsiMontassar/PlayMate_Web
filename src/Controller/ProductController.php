<?php

namespace App\Controller;
use App\Entity\user;
use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager
            ->getRepository(Product::class)
            ->findAll();

        return $this->render('Back/GestionProduit/Produit/Produit.html.twig', [
            'products' => $products,
        ]);
    }
    #[Route('/profile', name: 'app_user_product')]
    public function userProduct(Security $security, EntityManagerInterface $entityManager): Response
    {
        $userIdentifier = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
    
        $products = $entityManager->getRepository(Product::class)->findBy(['idfournisseur' => $user]);
        
        // Render the template with the tournaments
        return $this->render('Back/GestionProduit/Produit/profileproduct.html.twig', [
            'products' => $products,
        ]);
    }
    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Security $security, Request $request, EntityManagerInterface $entityManager): Response
    {   
        $userIdentifier = $security->getUser()->getUserIdentifier();
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            $nom = $form->get('nom')->getData();
            $description = $form->get('description')->getData();
            $prix = $form->get('prix')->getData();
            if (!preg_match('/^[a-zA-Z]+$/', $nom) || !preg_match('/^[a-zA-Z]+$/', $description)) {
                // Gérer l'erreur, par exemple afficher un message à l'utilisateur
                // Rediriger vers le formulaire avec un message d'erreur
                return $this->render('Back/GestionProduit/Produit/new.html.twig', [
                    'product' => $product,
                    'form' => $form->createView(),
                    'error_message' => 'Le nom et le gouvernorat doivent contenir uniquement des lettres.'
                ]);
            }
              // Vérifier si le prix est un entier
              if (!is_numeric($prix)) {
                // Gérer l'erreur, par exemple afficher un message à l'utilisateur
                // Rediriger vers le formulaire avec un message d'erreur
                return $this->render('Back/GestionProduit/Produit/new.html.twig', [
                    'product' => $product,
                    'form' => $form->createView(),
                    'error_message' => 'Le prix doit être un entier.'
                ]);
            }
            $product->setNom($nom);
            $product->setDescription($description);
            $product->setPrix($prix);
            $imageFile = $form['image']->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('terrain_images_directory'),
                    $newFilename
                );
                $product->setImage($newFilename);
            }   
            $product->setIdfournisseur($user);
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_product', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionProduit/Produit/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('Back/GestionProduit/Produit/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, int $id): Response
    {   
        
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
                throw $this->createNotFoundException('Produit non trouvé');
            }
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form['image']->getData();
            if ($imageFile) {
                        $newFilename = uniqid().'.'.$imageFile->guessExtension();
                        $imageFile->move(
                            $this->getParameter('terrain_images_directory'),
                        $newFilename
                        );
                        $product->setImage($newFilename);
                    }
            $entityManager->flush();

            return $this->redirectToRoute('app_user_product', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/GestionProduit/Produit/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager, int $id): Response
    {   
        $product = $entityManager->getRepository(product::class)->find($id); 
        if (!$product) {
                throw $this->createNotFoundException('product non trouvé');
            } 
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_product', [], Response::HTTP_SEE_OTHER);
    }
}
