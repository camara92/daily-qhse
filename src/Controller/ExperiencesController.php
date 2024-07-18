<?php

namespace App\Controller;

use App\Entity\Experience;
use App\Form\ExperiencesType;
use App\Repository\ExperienceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExperiencesController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/experiences', name: 'app_experiences')]
    public function index(): Response
    {
        return $this->render('experiences/index.html.twig', [
            'controller_name' => 'ExperiencesController',
        ]);
    }

    #[Route("/experience_add", name: "add_exp")]
    public function Addexperience(Request $request)
    {

       
        $livre = new Experience();

        $form = $this->createForm(ExperiencesType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           

        
            $this->entityManager->persist($livre);
            $this->entityManager->flush();

    
            $this->addFlash('success', 'Votre expérience a été ajouté avec succès.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('experiences/ajouter.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }
    #[Route("/experience-show", name:"show_experience")]
    public function ShowExperience(ExperienceRepository $experienceRepository){


        return $this->render('experiences/liste.html.twig', [
            // 'livres' => $livres,
            'livres' => $experienceRepository->findAll(), 
            

        ]);
    }
}
