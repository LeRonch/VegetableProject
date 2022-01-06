<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FinalisationController extends AbstractController
{
    /**
     * @Route("/finalisation", name="finalisation")
     */
    public function index(): Response
    {
        return $this->render('finalisation.html.twig', [
            'controller_name' => 'FinalisationController',
        ]);
    }
}
