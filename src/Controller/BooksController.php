<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Livres;
use App\Entity\Commentaires;
use App\Entity\Notes;
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
     * @Route("/singlebook/commentary/{id}", name="allBooksCommentary")
     */
    public function getSinglebooksCommentary($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Commentaires::class);
        $books = $repository->findBy(["livre" => $id]);
        return new Response($serializer->serialize($books, 'json', ['groups' => 'show_commentary', 'circular_reference_handler' => function ($object) {
            return $object->getId();
        }]));
    }

     /**
     * @Route("/singlebook/notes/{id}", name="singleBookNotes")
     */
    public function getSinglebooksNotes($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Notes::class);
        $books = $repository->findBy(["livre" => $id]);
        return new Response($serializer->serialize($books, 'json', ['groups' => 'show_notes', 'circular_reference_handler' => function ($object) {
            return $object->getId();
        }]));
    }

     /**
     * @Route("/singlebook/notes/average/{id}", name="singleBookNotesMoyenne")
     */
    public function getSinglebooksNotesMoyenne($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Notes::class);
        $books = $repository->findBy(["livre" => $id]);
        $moyenne = 0;
        foreach($books as $key => $value){
            $moyenne += $value->getValue();
        }
        $moyenne = $moyenne / sizeof($books);
        $result = ['average' => $moyenne];
        return new Response($serializer->serialize($result, 'json'));
    }

     /**
     * @Route("/singlebook/likes/{id}", name="singleBookLikes")
     */
    public function getSingleBookLikes($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Livres::class);
        $books = $repository->find($id);
        return new Response($serializer->serialize($books, 'json', ['groups' => 'show_likes', 'circular_reference_handler' => function ($object) {
            return $object->getId();
        }]));
    }

    /**
     * @Route("/singlebook/{id}", name="singleBook")
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
     * @Route("/singlebook/category/{id}", name="categoryBook")
     */
    public function getSinglebookByCategory($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Livres::class);
        $books = $repository->findBy(["category" => $id]);

        return new Response($serializer->serialize($books, 'json', ['groups' => 'show_commentary', 'circular_reference_handler' => function ($object) {
            return $object->getId();
        }]));
    }


}
