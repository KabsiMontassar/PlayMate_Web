<?php 
namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class GoogleController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/google", name="connect_google_start")
     * @param ClientRegistry $clientRegistry
     * @return RedirectResponse
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect();
    }

    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/google/check", name="connect_google_check")
     * @param Request $request
     * @param ClientRegistry $clientRegistry
     * @return JsonResponse
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
       if (!$this->getUser()) {
            return new JsonResponse(['error' => 'User not found'], 400);
        }else{
           return $this->redirectToRoute('app_Home');
        }
     
    }
}
