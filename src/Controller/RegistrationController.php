<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
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
    public function register(Request $request): Response
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

        return $this->json(['message' => 'ça marche youpi', 'status' => 201]);
    }
}
