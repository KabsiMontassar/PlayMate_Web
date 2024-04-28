<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twilio\Rest\Client;

class SMS extends AbstractController
{
    
    #[Route('/sms', name: 'sms')]
    public function smsSend(Request $request): Response
    {
        // Retrieve the phone number from the form submission
        $phoneNumber = $request->request->get('Phone');

        // Your Twilio Account SID, Auth Token, and Twilio phone number
        $accountSid = $_ENV['TWILIO_ACCOUNT_SID'];
        $authToken = $_ENV['TWILIO_AUTH_TOKEN'];
        $twilioPhoneNumber = $_ENV['TWILIO_NUMBER'];

        // Initialize Twilio client
        $twilio = new Client($accountSid, $authToken);

        // Send SMS
        $message = $twilio->messages
            ->create(
                $phoneNumber, // Destination phone number from the form
                [
                    'from' => $twilioPhoneNumber, // Your Twilio phone number
                    'body' => 'Your payment has been successfully processed. Thank you for visiting our event Esprit made by TANIT ONLINE !'
                ]
            );

            return $this->redirectToRoute('app_Evenement');
        }
}