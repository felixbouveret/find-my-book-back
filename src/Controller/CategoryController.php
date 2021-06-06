<?php

namespace App\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryController extends AbstractController
{
    /**
     * @Route("/categories/all", name="allCategories")
     */
    public function getAllCategories(SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Categorie::class);
        $categories = $repository->findAll();

        return new Response($serializer->serialize($categories, 'json'));
    }

    /**
     * @Route("/categories/limit/{number}", name="allCategoriesLimited")
     */
    public function getAllCategoriesLimited($number, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Categorie::class);
        $categories = $repository->findAllLimited($number);

        return new Response($serializer->serialize($categories, 'json'));
    }

    /**
     * @Route("/categories/{id}/books", name="allCategoriesLimited")
     */
    public function getBookByCategories($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Categorie::class);
        $categories = $repository->find($id);
        $books = $categories->getBooks();

        return new Response($serializer->serialize($books, 'json'));
    }

    /**
     * @Route("/categories/{id}", name="singleCategory, priority=2")
     */
    public function getSingleCategory($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Categorie::class);
        $categories = $repository->find($id);

        return new Response($serializer->serialize($categories, 'json'));
    }
}