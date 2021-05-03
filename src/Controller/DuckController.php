<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DuckController extends AbstractController
{
    /**
     * @Route("/duck", name="duck")
     */
    public function index(): Response
    {
        return $this->render('duck/index.html.twig', [
            'controller_name' => 'DuckController',
        ]);
    }
}
