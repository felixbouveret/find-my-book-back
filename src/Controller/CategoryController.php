<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/categories/{id}", name="singleCategory")
     */
    public function getSingleCategory($id, SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Livres::class);
        $categories = $repository->find($id);

        return new Response($serializer->serialize($categories, 'json'));
    }
}
