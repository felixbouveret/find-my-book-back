<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        LoginFormAuthenticator $authenticator,
        UserRepository $userRepository
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->authenticator = $authenticator;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/api/register", name="app_register")
     */
    public function register(Request $request, \Swift_Mailer $mail): Response
    {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'];
        $email = $data['email'];
        $password = $data['password'];

        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);

        if (!is_null($user)) {
            return $this->json(['message' => 'User déjà créé', 'status' => 401]);
        }

        $user = new User();

        $user->setUsername($username);
        $user->setEmail($email);

        // encode the plain password
        $user->setPassword(
                $this->passwordEncoder->encodePassword(
                $user,
                $password
            )
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        // do anything else you need here, like send an email

        $message = (new \Swift_Message('Bonjour ' . $user->getUsername() . ' votre compte est crée !'))
        ->setFrom('ben33127@gmail.com')
        ->setTo($user->getEmail())
        ->setBody(
            $this->renderView(
                'mail/registration.html.twig',
                ['name' => $user->getUsername()]
            ),
            'text/html'
        );

        $mail->send($message);

        return $this->json(['message' => 'User registered', 'status' => 201]);
    }
}