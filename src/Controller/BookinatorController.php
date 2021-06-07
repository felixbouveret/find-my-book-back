<?php

namespace App\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BookinatorController extends AbstractController
{
    /**
     * @Route("/bookinator", name="bookinator")
     */
    public function firstStep(Request $request, SerializerInterface $serializerInterface): Response
    {
        $req = $request->request->all();
        $arrayId = $req['categorieChoosen'];
        $cat = $this->getDoctrine()->getRepository(Categorie::class);
        $rtr = $cat->findByMultipleId($arrayId);

        return new Response($serializerInterface->serialize($rtr, 'json'));
    }
}