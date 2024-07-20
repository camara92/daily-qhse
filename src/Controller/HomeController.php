<?php

namespace App\Controller;

use App\Repository\ExperienceRepository;
use App\Repository\FAQRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ExperienceRepository $showexperience, FAQRepository $questionRepository): Response
    {
         // Appel de la méthode pour récupérer les 5 premières questions
         $questions = $questionRepository->findFirstFiveQuestions();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'experiences'=> $showexperience->findAll(),
            'qa'=> $questions
        ]);
    }
}
