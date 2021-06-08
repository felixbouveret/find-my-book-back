<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Livres;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user/likes/{id}/all", name="getAllBooksLiked")
     */
    public function getAllBooksLiked($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $booksLiked = $repository->getAllBooksLiked($id);

        return new Response($serializer->serialize($booksLiked, 'json', ['groups' => 'show_commentary', 'circular_reference_handler']));
    }

    /**
     * @Route("/user/{user_id}/likes/{book_id}/add", name="addOneBookToUser")
     */
    public function addOneBookToUser($user_id, $book_id, SerializerInterface $serializer): Response
    {
        $user_repository = $this->getDoctrine()->getRepository(User::class);
        $book_repository = $this->getDoctrine()->getRepository(Livres::class);
        $entityManager = $this->getDoctrine()->getManager();

        $user = $user_repository->find($user_id);
        $book = $book_repository->find($book_id);

        if ($user === null) {
            return new Response($serializer->serialize(["message" => "User not found"], 'json'));
        } elseif ($book === null) {
            return new Response($serializer->serialize(["message" => "Book not found"], 'json'));
        }

        $isBookLiked = $user_repository->isBookLiked($user_id, $book_id);

        if ($isBookLiked) {
            return new Response($serializer->serialize(["message" => "Book already liked"], 'json'));
        }

        $user->addLike($book);

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response($serializer->serialize(["message" => "Like successfully added"], 'json'));
    }

    /**
     * @Route("/user/{user_id}/likes/{book_id}/delete", name="deleteOneBookToUser")
     */
    public function deleteOneBookToUser($user_id, $book_id, SerializerInterface $serializer): Response
    {
        $user_repository = $this->getDoctrine()->getRepository(User::class);
        $book_repository = $this->getDoctrine()->getRepository(Livres::class);
        $entityManager = $this->getDoctrine()->getManager();

        $user = $user_repository->find($user_id);
        $book = $book_repository->find($book_id);

        if ($user === null) {
            return new Response($serializer->serialize(["message" => "User not found"], 'json'));
        } elseif ($book === null) {
            return new Response($serializer->serialize(["message" => "Book not found"], 'json'));
        }

        $isBookLiked = $user_repository->isBookLiked($user_id, $book_id);

        if (!$isBookLiked) {
            return new Response($serializer->serialize(["message" => "Book already deleted"], 'json'));
        }

        $user->removeLike($book);

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response($serializer->serialize(["message" => "Like successfully deleted"], 'json'));
    }
}
