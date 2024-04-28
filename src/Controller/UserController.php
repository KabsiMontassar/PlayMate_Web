<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserUpdateType;
use App\Form\UserPasswordType;
use App\Form\forgetpassword;
use App\Form\Login;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Controller\HomeController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use App\Security\EmailVerifier;
use Symfony\Component\Security\Core\User\UserInterface;
use Knp\Component\Pager\PaginatorInterface;



#[Route('/user')]
class UserController extends AbstractController
{
  
    private EmailVerifier $emailVerifier;
    private MailerInterface $mailer;

    public function __construct(EmailVerifier $emailVerifier, MailerInterface $mailer)
    {
        $this->emailVerifier = $emailVerifier;
        $this->mailer = $mailer;
       

    }
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Fetch all users query
        $query = $userRepository->createQueryBuilder('u')
            ->getQuery();
    
        // Paginate the query
        $pagination = $paginator->paginate(
            $query, // Query to paginate
            $request->query->getInt('page', 1), // Current page number
            5 // Items per page
        );
    
        return $this->render('Back/GestionUser/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    #[Route('/dashboard', name: 'app_user_dashboard', methods: ['GET'])]
    public function dashboard(UserRepository $userRepository): Response
    {
        return $this->render('Back/GestionUser/dashboard.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

   

  
    #[Route('/profile', name: 'app_user_profile', methods: ['GET', 'POST'])]
    public function profile(UserPasswordEncoderInterface $encoder,Security $security,Request $request, EntityManagerInterface $entityManager): Response
    {
        $userIdentifier = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
  
       
        $form1 = $this->createForm(UserUpdateType::class , $user , ['validation_groups' => ['update_profile']]);
        $form2 = $this->createForm(UserPasswordType::class  );
    
        // Handle form submissions
        $form1->handleRequest($request);
        $form2->handleRequest($request);
    
        // Check which form was submitted
        if ($form1->isSubmitted() && $form1->isValid()) {
 
        
          
            $formData = $form1->getData();
            // Process form 1 (update user information)
           $imageFile =  $form1->get('imageFile')->getData();
           if ($imageFile) {
            // Generate a unique name for the file
            $newFilename = uniqid().'.'.$imageFile->guessExtension();

    
            // Move the file to the desired directory
            $imageFile->move(
                $this->getParameter('image_directory'), // Path defined in services.yaml or config/packages/framework.yaml
                $newFilename
            );
           
            $formData->setImage($newFilename);
        }

            $entityManager->persist($formData);
            $entityManager->flush();
           

            $this->addFlash('success', 'Profile updated successfully');

        }
      
        if ($form2->isSubmitted() && $form2->isValid()) {


            if ($form2->get('NewPassword')->getData() == $form2->get('ConfirmPassword')->getData()) {

                if($encoder->isPasswordValid($user, $form2->get('CurrentPassword')->getData())){
                    $user->setPassword($encoder->encodePassword($user, $form2->get('NewPassword')->getData()));
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $this->addFlash('success', 'Password updated successfully');
                } else {
                    $this->addFlash('danger', 'Current password is incorrect');
                }

            
            } else {
                $this->addFlash('danger', 'New password and confirm password do not match');

            }
           
            // Process form 2 (update user password)
           if($form2->get('CurrentPassword')->getData() == $user ->getPassword()){


              if($form2->get('NewPassword')->getData() == $form2->get('ConfirmPassword')->getData()){
                
                $user->setPassword($form2->get('NewPassword')->getData());
                $entityManager->persist($user);
                $entityManager->flush();
                          
              }else{
                   $this->addFlash('danger', 'New password and confirm password do not match');
                  
              }
        }
        else{
        }
    }
    
      
        return $this->render('Front/ProfileElements/userProfile.html.twig', [
           
        ]);
    }


    #[Route('/forgot_password', name: 'app_forgot_password', methods: ['GET', 'POST'])]

    public function forgetPassword(): Response
    {
    
        return $this->render('Back/GestionUser/ForgetPassword.html.twig');
    }
    
    



#[Route('/resetpassword/{email}', name: 'app_reset_password_email', methods: ['POST'])]
public function resetPasswordEmail(Request $request, EntityManagerInterface $entityManager): Response
{

    $email = $request->attributes->get('email');
  

    $user = $entityManager->getRepository(User::class)->findOneByEmail($email);
    if ($user) {
        $verificationCode = rand(100000, 999999);
        $user->setVerificationCode($verificationCode);
        $entityManager->flush();

        // $email = (new Email())
        // ->from(new Address('playmatepidev@gmail.com', 'PlayMate Bot'))
        // ->to($user->getEmail())
        // ->subject('Verification Code')
        // ->text('Your verification code is: ' . $verificationCode);
        $htmlTemplate = $this->renderView('registration/Verification_Code.html.twig', [
            'verification_code' => $verificationCode,
        ]);
        $email = (new Email())
        ->from(new Address('playmatepidev@gmail.com', 'PlayMate Bot'))
        ->to($user->getEmail())
        ->subject('Verification Code')
        ->html($htmlTemplate);
        $this->mailer->send($email);
    
        
         
         
      
        
      
      

        return new Response('Verification code sent successfully', Response::HTTP_OK);
    } else {
      
        return new Response('Email not found', Response::HTTP_OK);
    }
}

#[Route('/resetpassword/{email}/{code}', name: 'app_reset_password_verification_code', methods: ['POST'])]
public function resetPasswordVerificationCode(Request $request, EntityManagerInterface $entityManager): Response
{
  
    $email = $request->attributes->get('email');
    $verificationCode = $request->attributes->get('code');


    $user = $entityManager->getRepository(User::class)->findOneByEmail($email);
    if ($user && $user->getVerificationCode() == $verificationCode) {
       
  

        return new Response('Verification successful', Response::HTTP_OK);
    } else {
       
        return new Response('Verification failed', Response::HTTP_OK);
    }
}

#[Route('/resetpassword/{email}/{code}/{newpassword}/{confirmpassword}', name: 'app_reset_password_complete', methods: ['POST'])]
public function resetPasswordComplete(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder): Response
{
    $email = $request->attributes->get('email');
    $verificationCode = $request->attributes->get('code');
    $newPassword = $request->attributes->get('newpassword');
    $confirmPassword = $request->attributes->get('confirmpassword');

    // Retrieve the user based on the email
    $user = $entityManager->getRepository(User::class)->findOneByEmail($email);
    
    
            // Update user's password
            $user->setPassword($encoder->encodePassword($user, $newPassword));
            $entityManager->flush();
            
            // Clear the verification code
          //  $user->setVerificationCode(null);
         //   $entityManager->flush();

          
            return new Response('Password reset successfully', Response::HTTP_OK);
            

      
   
}
#[Route('/inverStatus/{email}', name: 'app_user_inverStatus', methods: ['POST'])]
public function invertstatus(Request $request , EntityManagerInterface $entityManager): Response
  {

      $user = $entityManager->getRepository(User::class)->findOneByEmail($request->attributes->get('email'));
      if (!$user) {
          $this->addFlash('danger', 'Email not found');
          return new Response('error', Response::HTTP_OK);
       }

       $user->setIsVerified(!$user->isVerified());
       $entityManager->persist($user);
       $entityManager->flush();
       return new Response('success', Response::HTTP_OK);
      

  }

  /**
 * @Route("/resend-activation-email", name="app_resend_activation_email", methods={"POST"})
 */
public function resendActivationEmail(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager)
{
    $email = $request->request->get('email');

    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    if(!$user) {
        return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }

    $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('playmatepidev@gmail.com', 'PlayMate Bot'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

    return new JsonResponse(['message' => 'Activation email resent successfully']);
}

  


}

