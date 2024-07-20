<?php

namespace App\Controller;

use App\Entity\FAQ;
use App\Form\FAQType;
use App\Repository\FAQRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


class QuestionsController extends AbstractController
{
    #[Route('/questions', name: 'app_questions')]
    public function index(): Response
    {
        return $this->render('questions/index.html.twig', [
            'controller_name' => 'QuestionsController',
        ]);
    }
    #[Route('/ques-ans', name: 'app_question-aswer')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $faq = new FAQ();
        $form = $this->createForm(FAQType::class, $faq);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($faq);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('questions/question-answer.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/show-question_answers', name: 'app_question-aswer_show')]
    public function show(Request $request, FAQRepository $fAQRepository): Response
    {
       
      

        return $this->render('questions/show.qa.html.twig', [
            'qa' => $fAQRepository->findAll(),
        ]);
    }
}
