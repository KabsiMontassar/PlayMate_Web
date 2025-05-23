<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;


class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    private UrlGeneratorInterface $urlGenerator;	

    public const LOGIN_ROUTE = 'app_login';
    private $entityManager;
    public function __construct(UrlGeneratorInterface $urlGenerator , EntityManagerInterface $entityManager)
    {
    
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;

      
    }

    public function authenticate(Request $request ): Passport
    {
        $email = $request->request->get('email', '');

        if($request->request->get('g-recaptcha-response') == null)
        {
            throw new CustomUserMessageAuthenticationException('Please check the captcha');
        }
        
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

     
       
        $request->getSession()->set(Security::LAST_USERNAME, $email);

    
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
              
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
       

     
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

       
      return new RedirectResponse($this->urlGenerator->generate('app_Home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    public function forgotPassword(Request $request, UserRepository $userRepository )
    {
    }
    
}
