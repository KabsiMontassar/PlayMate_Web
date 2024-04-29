<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Repository\EquipeRepository;
use App\Form\EquipeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Membreparequipe;


use Symfony\Component\Security\Core\Security;
use App\Form\UserType;
use App\Form\UserUpdateType;
use App\Form\UserPasswordType;
use App\Form\forgetpassword;
use App\Form\Login;
use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Controller\HomeController;
use Knp\Component\Pager\PaginatorInterface;




#[Route('/equipe')]
class EquipeController extends AbstractController
{
    #[Route('/', name: 'app_equipe_index', methods: ['GET'])]
    public function index(
        PaginatorInterface $paginator,
        EquipeRepository $rep,
        Request $request
    ): Response {
      $sort = $request->query->get('sort', 'nbrejoueur');

        if( $request->query->get('tritype') == 'members-asc' ){
            $order = $request->query->get('order', 'asc');
        }else{
            $order = $request->query->get('order', 'desc');
        }



        $queryBuilder = $rep->createQueryBuilder('e')
            ->orderBy('e.' . $sort, $order);

        // Recherche par nomEquipe
        $searchTerm = $request->query->get('search');
        if ($searchTerm) {
            $queryBuilder->andWhere('e.nomequipe LIKE :search')
                ->setParameter('search', '%' . $searchTerm . '%');
        }

        $query = $queryBuilder->getQuery();

        $equipes = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('Back/Gestionequipe/Equipe/Equipe.html.twig', [
            'equipes' => $equipes,
            'sort' => $sort,
            'order' => $order,
        ]);
    }
   
    
    #[Route('/new', name: 'app_equipe_new', methods: ['GET', 'POST'])]
    public function new(Security $security,Request $request, EntityManagerInterface $entityManager): Response
    {
        $userIdentifier = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
          
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($equipe);
            $entityManager->flush();
           $membreparequipe = new Membreparequipe();
              $membreparequipe->setIdequipe($equipe);
                $membreparequipe->setIdmembre($user);
            $entityManager->persist($membreparequipe);
            $entityManager->flush();
        

            return $this->redirectToRoute('First', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Front/ProfileElements/Forms/FormAddTeam.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    #[Route('/{idequipe}', name: 'app_equipe_show', methods: ['GET'])]
    public function show(Equipe $equipe): Response
    {
        return $this->render('Back/Gestionequipe/Equipe/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    #[Route('/{idequipe}/edit', name: 'app_equipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('First', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Front/ProfileElements/Forms/FormAddTeam.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    #[Route('/{idequipe}', name: 'app_equipe_delete', methods: ['POST'])]
    public function delete(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipe->getIdequipe(), $request->request->get('_token'))) {
            $entityManager->remove($equipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
    }

    //app equipe profile 
    #[Route('/profile', name: 'app_equipe_profile', methods: ['GET'])]
    public function profile(EntityManagerInterface $entityManager): Response
    {
        $equipes = $entityManager
            ->getRepository(Equipe::class)
            ->findAll();

        return $this->render('Front/Equipe/Profile.html.twig', [
            'equipes' => $equipes,
        ]);
    }


     
   
}
