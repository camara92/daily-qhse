<?php

namespace App\Controller;

use App\Repository\ExperienceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ExperienceRepository $showexperience): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'experiences'=> $showexperience->findAll()
        ]);
    }
}
