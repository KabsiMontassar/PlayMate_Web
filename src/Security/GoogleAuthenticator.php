<?php 
namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GoogleAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
    }

    public function supports(Request $request): bool
    {
        return $request->getPathInfo() == '/connect/google/check' && $request->isMethod('GET');
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->clientRegistry->getClient('google'));
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?User
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $this->clientRegistry
            ->getClient('google')
            ->fetchUserFromToken($credentials);

        $email = $googleUser->getEmail();
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['googleId' => $googleUser->getId()]);

        if (!$user) {
            $user = $this->em->getRepository(User::class)
                ->findOneBy(['email' => $email]);

            if (!$user) {
                $user = new User();
                $user->setEmail($email);
                $user->setGoogleId($googleUser->getId());
                $user->setName($googleUser->getFirstName());
                $user->setPassword('');	
                $user->setRole('Membre');                     
                
               
                $this->em->persist($user);
                $this->em->flush();
            }
        }

        return $user;
    }

    public function onAuthenticationSuccess(Request $request, $token, $providerKey): ?Response
    {
        return new RedirectResponse($this->router->generate('app_Home'));
    }

    public function onAuthenticationFailure(Request $request, $exception): ?Response
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }

    public function start(Request $request, $authException = null): Response
    {
        return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}



