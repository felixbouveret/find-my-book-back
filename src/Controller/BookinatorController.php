<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookinatorController extends AbstractController
{
    /**
     * @Route("/akinator", name="akinator")
     */
    public function akinatorMain(Request $request): Response
    {
        $dataIn = $this->getJson($request);
        $answer = $dataIn->answer;
        $rtr = $this->determinePref($answer);

        return $this->json($rtr);
    }

    protected function getJson($request)
    {
        $rtr = json_decode($request->getContent());
        return $rtr;
    }

    protected function determinePref($answer)
    {
        $age = $answer->age;
        $oeuvre = $answer->length;
        $typeOfBook = $answer->typeOfBook;
        $styleOfBook = $answer->styleOfBook;

        $catSugg = [];
        array_push($catSugg, $styleOfBook);

        $bookLenght = [
            "min" => 0,
            "max" => 0
        ];

        switch ($oeuvre) {
            case "long":
                $bookLenght["min"] = 500;
                $bookLenght["max"] = -1;
                break;

            case "short":
                $bookLenght["min"] = -1;
                $bookLenght["max"] = 300;
                break;
            case "duncare":
                $bookLenght["min"] = -1;
                $bookLenght["max"] = -1;
                break;

            default:
                throw new \Exception("Error", 1);
                break;
        }


        if ($age <= 18) {
            array_push($catSugg, "Jeunesse");
            array_push($catSugg, "Prout");
            array_push($catSugg, "Caca");
            array_push($catSugg, "Pipi");
        } elseif ($age > 18 && $age <= 30) {
            array_push($catSugg, "Presque jeune");
            array_push($catSugg, "Prout");
            array_push($catSugg, "Caca");
            array_push($catSugg, "Pipi");
        } elseif ($age > 30) {
            array_push($catSugg, "Vieux");
            array_push($catSugg, "Prout");
            array_push($catSugg, "Caca");
            array_push($catSugg, "Pipi");
        }

        $rtr = [
            "pop" => $typeOfBook,
            "cat" => $catSugg,
            "length" => $bookLenght
        ];

        return $rtr;
    }
}