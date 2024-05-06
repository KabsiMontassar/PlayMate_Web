<?php

namespace App\Controller;
use App\Entity\user;
use App\Entity\Product;
use App\Entity\Commande;
use App\Form\ProductType;
use App\Form\TestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/product')]
class ProductController extends AbstractController
{
    
    
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator,EntityManagerInterface $entityManager): Response
    {
    
        $query = $entityManager->getRepository(Product::class)->createQueryBuilder('p');

        $filter = ['tri par nom', 'tri par prix'];
        $filterfilter = $request->query->get('datedebut');
        if ($filterfilter && in_array($filterfilter, $filter)) {
            if ($filterfilter === 'tri par nom') {
                $query->orderBy('p.nom', 'ASC');
            } else if ($filterfilter === 'tri par prix') {
                $query->orderBy('p.prix', 'ASC');
            }
        }
        
        // Apply search if search term provided in the request
        $searchTerm = $request->query->get('search');
        if ($searchTerm) {
            $query->andWhere('p.nom LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }


        $pagination = $paginator->paginate(
            $query, // Requête à paginer
            $request->query->getInt('page', 1), // Numéro de page par défaut
            3// Nombre d'éléments par page
        );

        return $this->render('Back/GestionProduit/Produit/Produit.html.twig', [
            'pagination' => $pagination,
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

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
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

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
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

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/commander/{idproduct}', name: 'app_commander', methods: ['GET','POST'])]
public function commander(Security $security, Request $request, EntityManagerInterface $entityManager): Response
{

        $idproduct = $request->attributes->get('idproduct');
        $product = $entityManager->getRepository(Product::class)->findOneBy(['id' =>$idproduct]); 
       

        $userIdentifier = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
       
        $commande = new Commande();
        $commande->setIdproduit($product);
        $commande->setIdmembre($user);

        // Obtenez la date courante
        $dateCourante = new \DateTime();

        $dateCouranteString = $dateCourante->format('Y-m-d H:i:s');
        $commande->setDatecommande($dateCouranteString);
        

        // Enregistrez la commande dans la base de données
        $entityManager->persist($commande);
        $entityManager->flush();

        return new Response('success', Response::HTTP_OK);
 
}
#[Route('/generate_qr_code/{productname}', name: 'product_generate_qr_code')]
public function generateQrCode(Request $request, QrCodeService $qrcodeService,EntityManagerInterface $entityManager): JsonResponse
{
    $productName = $request->attributes->get('productname');

    // Générer le QR code pour le produit spécifique
    $qrCode = $qrcodeService->qrcode($productName);

     // Écrire le fichier dans le dossier spécifié
     $qrCode->writeFile($directory . $productName . '.png');

     // Retourner une réponse JSON avec le chemin du fichier
     return new JsonResponse(['qrCodePath' => $directory . $productName . '.png']);
}




}
