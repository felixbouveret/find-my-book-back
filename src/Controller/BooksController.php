<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Livres;
use App\Entity\Commentaires;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class BooksController extends AbstractController
{
    /**
     * @Route("/books/all", name="allBooks")
     */
    public function getAllBooks(SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Livres::class);
        $books = $repository->findAll();

        return new Response($serializer->serialize($books, 'json', ['groups' => 'show_commentary', 'circular_reference_handler' => function ($object) {
            return $object->getId();
        }]));
    }

    /**
     * @Route("/books/commentary/{id}", name="allBooksCommentary")
     */
    public function getAllBooksCommentary($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Commentaires::class);
        $books = $repository->findBy(["livre" => $id]);
        return new Response($serializer->serialize($books, 'json', ['groups' => 'show_commentary', 'circular_reference_handler' => function ($object) {
            return $object->getId();
        }]));
    }

    /**
     * @Route("/books/{id}", name="singleBook")
     */
    public function getSingleBook($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Livres::class);
        $book = $repository->find($id);

        return new Response($serializer->serialize($book, 'json', ['groups' => 'show_commentary', 'circular_reference_handler' => function ($object) {
            return $object->getId();
        }]));
    }

     /**
     * @Route("/books/search/{research}", name="researchBook")
     */
    public function getBooksByResearch($research, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Livres::class);
        $search = $repository->findBy(["auteur" => $research]);
        $search = $repository->findBy(["isbn_code" => $research]);
        $search = $repository->findBy(["name" => $research]);
        $search = $repository->findBy(["id" => $research]);
        if($search === []){
            dd("test");
        }

        return new Response($serializer->serialize($search, 'json'));
    }

    /**
     * @Route("/books/category/{id}", name="categoryBook")
     */
    public function getBooksByCategory($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Livres::class);
        $books = $repository->findBy(["category" => $id]);

        return new Response($serializer->serialize($books, 'json', ['groups' => 'show_commentary', 'circular_reference_handler' => function ($object) {
            return $object->getId();
        }]));
    }


}
