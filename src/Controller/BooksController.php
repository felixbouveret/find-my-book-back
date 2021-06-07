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
use Algolia\SearchBundle\SearchService;
use Algolia\SearchBundle\Responses\SearchServiceResponse;
use App\Entity\Categorie;

class BooksController extends AbstractController
{

    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * @Route("/books/all", name="allBooks")
     */
    public function getAllBooks(SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Livres::class);
        $books = $repository->findAll();

        return new Response($serializer->serialize($books, 'json', ['groups' => 'show_commentary', 'circular_reference_handler']));
    }

    /**
     * @Route("/books/average/{limit}", name="allBestGradesBooks")
     */
    public function allBestGradesBooks($limit, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Livres::class);
        $books = $repository->findAll();

        $allBooksGrades = array();
        foreach ($books as $key => $value) {
            $allBooksGrades[$key] = $value->getNotes()->getValues();
        }

        $allBooksAverage = array();

        foreach ($allBooksGrades as $key => $value) {
            $average = 0;
            foreach ($allBooksGrades[$key] as $id => $notes) {
                $average += $notes->getValue();
            }
            if (sizeof($allBooksGrades[$key]) === 0) {
                break;
            } else {
                $allBooksAverage[$key] = ["book" => $value[0]->getLivre(), "average" => round($average / sizeof($allBooksGrades[$key], 2))];
            }
        }
        arsort($allBooksAverage);
        $result = array_slice($allBooksAverage, 0, intval($limit), true);
        return new Response($serializer->serialize($result, 'json'));
    }

    /**
     * @Route("/singlebook/commentary/{id}", name="allBooksCommentary")
     */
    public function getSinglebooksCommentary($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Commentaires::class);
        $books = $repository->findBy(["livre" => $id]);
        return new Response($serializer->serialize($books, 'json', ['groups' => 'show_commentary', 'circular_reference_handler']));
    }

    /**
     * @Route("/singlebook/notes/{id}", name="singleBookNotes")
     */
    public function getSinglebooksNotes($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Notes::class);
        $books = $repository->findBy(["livre" => $id]);
        return new Response($serializer->serialize($books, 'json', ['groups' => 'show_notes', 'circular_reference_handler']));
    }

    /**
     * @Route("/singlebook/notes/average/{id}", name="singleBookNotesMoyenne")
     */
    public function getSinglebooksNotesMoyenne($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Notes::class);
        $books = $repository->findBy(["livre" => $id]);
        $moyenne = 0;
        foreach ($books as $key => $value) {
            $moyenne += $value->getValue();
        }
        if (sizeof($books) === 0) {
            return new Response($serializer->serialize("There is no notes for this book", 'json'));
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
        return new Response($serializer->serialize($books, 'json', ['groups' => 'show_likes', 'circular_reference_handler']));
    }

    /**
     * @Route("/singlebook/{id}", name="singleBook")
     */
    public function getSingleBook($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Livres::class);
        $book = $repository->find($id);

        return new Response($serializer->serialize($book, 'json', ['groups' => 'show_commentary', 'circular_reference_handler']));
    }

    /**
     * @Route("/books/search/{research}", name="researchBook")
     */
    public function getBooksByResearch($research, SerializerInterface $serializer): Response
    {
        $em = $this->getDoctrine()->getManagerForClass(Livres::class);
        $books = $this->searchService->search($em, Livres::class, $research);

        $books = $this->searchService->search($em, Livres::class, $research, [
            'page' => 0,
            'hitsPerPage' => 10
        ]);

        return new Response($serializer->serialize($books, 'json'));
    }
}
