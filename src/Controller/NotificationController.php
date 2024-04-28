<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Form\NotificationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notification')]
class NotificationController extends AbstractController
{
   

   


 

    #[Route('/{id}', name: 'app_notification_delete', methods: ['POST'])]
    public function delete(Request $request, Notification $notification, EntityManagerInterface $entityManager): Response
    {
            $notification->getId();
            $entityManager->remove($notification);
            $entityManager->flush();
        

            return new Response('DeletedSuccesfuly', Response::HTTP_OK);
    }
}
