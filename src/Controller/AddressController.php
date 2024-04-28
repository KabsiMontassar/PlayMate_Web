<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;


class AddressController extends AbstractController
{
    /**
     * @Route("/get-address/{lat}/{lon}", name="get_address", methods={"GET"})
     */
    public function getAddress($lat, $lon): JsonResponse
    {
        // Prepare the URL for the Nominatim API request with French language preference
        $url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' . urlencode($lat) . '&lon=' . urlencode($lon) . '&zoom=18&addressdetails=1&accept-language=fr';
    
        // Create an HTTP client instance
        $httpClient = HttpClient::create();
    
        try {
            // Send a GET request to the Nominatim API
            $response = $httpClient->request('GET', $url);
    
            // Check if the request was successful (status code 200)
            if ($response->getStatusCode() === 200) {
                // Get the response content
                $content = $response->getContent();
    
                // Decode the JSON response
                $data = json_decode($content, true);
    
                // Check if the address components are available
                if ($data) {
                    // Construct the address string
    
                    // Return the address as JSON response
                    return new JsonResponse($data);
                } else {
                    // Handle missing address components
                    return new JsonResponse(['error' => 'Address components not found'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                // Handle non-200 HTTP status code
                return new JsonResponse(['error' => 'Failed to retrieve data from the API'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            // Handle any exceptions
            return new JsonResponse(['error' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
