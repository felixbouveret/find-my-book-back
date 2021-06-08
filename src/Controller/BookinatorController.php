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

        return new Response($serializerInterface->serialize($rtr, 'json'));
    }

    /**
     * @Route("/bookinator/secondstep", name="bookinatorsecondstep")
     */
    public function secondStep(Request $request){
        $req = $request->request->all();
        $arrayId = $req['bookChoosen'];
    }
}