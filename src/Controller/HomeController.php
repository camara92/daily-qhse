<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\ExperienceRepository;
use App\Repository\FAQRepository;
use App\Repository\ReferencesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ExperienceRepository $showexperience, FAQRepository $questionRepository, ArticleRepository $articleRepository , ReferencesRepository $referencesRepository): Response
    {
         // Appel de la méthode pour récupérer les 5 premières questions
         $questions = $questionRepository->findFirstFiveQuestions();
         $artilespremiers = $articleRepository->SeptpremiersArticle();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'experiences'=> $showexperience->findAll(),
            'references'=> $referencesRepository->findAll(),
            'qa'=> $questions, 
            'sevenarticles' => $artilespremiers
        ]);
    }
}
