<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReferencesController extends AbstractController
{
    #[Route('/references', name: 'app_references')]
    public function index(): Response
    {
        return $this->render('references/index.html.twig', [
            'controller_name' => 'ReferencesController',
        ]);
    }
}
