<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class MailController extends AbstractController
{
    #[Route('/mail{id}', name: 'product_mail')]
    public function EmailProduit(MailerInterface $mailer, ProductRepository $rep, $id): Response
    {
       
        $produit=$rep->find($id);
        $htmlContent = '<table>';
         
            $htmlContent .= '<tr>';
            $htmlContent .= '<td>' . $produit->getId() . '</td>'; 
            $htmlContent .= '<td>' . $produit->getNom() . '</td>'; 
            $htmlContent .= '<td>' . $produit->getDescription() . '</td>';
            $htmlContent .= '<td>' . $produit->getPrix() . '</td>';
            $htmlContent .= '</tr>';
        
        $htmlContent .= '</table>';
    
        
        $email = (new Email())
            ->from('seiflamti@gmail.com')
            ->to('seiflamti@gmail.com')
            ->subject('Liste produit')
            ->html($htmlContent);
    
        
        $mailer->send($email);
        return $this->redirectToRoute('app_Boutique');
         
    }

    
}