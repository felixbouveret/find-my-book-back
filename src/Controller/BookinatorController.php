<?php

namespace App\Controller;

use App\Entity\Livres;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BookinatorController extends AbstractController
{
    /**
     * @Route("/bookinator/firststep", name="bookinatorfirststep")
     */
    public function firstStep(Request $request, SerializerInterface $serializerInterface): Response
    {
        $req = $request->request->all();
        $arrayId = $req['categorieChoosen'];
        $books = $this->getDoctrine()->getRepository(Livres::class);
        $rtr = $books->findByMultipleId($arrayId);
        return new Response($serializerInterface->serialize($rtr, 'json', ['groups' => ['show_notes', 'circular_reference_handler']]));
    }

    /**
     * @Route("/bookinator/secondstep", name="bookinatorsecondstep")
     */
    public function secondStep(Request $request, SerializerInterface $serializerInterface) : Response
    {
        $req = $request->request->all();
        $arrayId = $req['bookChoosen'];

        $books = $this->getDoctrine()->getRepository(Livres::class);
        $arrayBooks = $books->getBestRatedBooksById($arrayId);
        $bestBook = $arrayBooks[0];

        $bookCat = $bestBook['book']->getCategorie()->getId();
        $bookDownSclaledNote = floor($bestBook['average']);

        $rtr = $books->getBookByCatAndRating($bookCat, $bookDownSclaledNote);

        return new Response($serializerInterface->serialize($rtr, 'json', ['groups' => ['show_notes', 'circular_reference_handler']]));
    }
}