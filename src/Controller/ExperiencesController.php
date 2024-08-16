<?php

namespace App\Controller;

use App\Entity\Experience;
use App\Form\ExperiencesType;
use App\Repository\ExperienceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
    public function Addexperience(Request $request, SluggerInterface $slugger)
    {

       
        $experience = new Experience();

        $form = $this->createForm(ExperiencesType::class, $experience);
        $form->handleRequest($request);
        $photo = $form->get('photo')->getData();
        if ($form->isSubmitted() && $form->isValid()) {

            // picture for experience à faire : 
                if ($photo) {
                    $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                    // Cela est nécessaire pour inclure en toute sécurité le nom du fichier dans l'URL.
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();
                    // stockage de média dans partie public et service yaml :
                    try {
                        $photo->move(
                            $this->getParameter('image_experiences'),
                            // image directory dans service yaml
                            $newFilename
                        );
                    } catch (FileException $e) {
                       //  information confirmation ajout media ? lol 
                    }
        
                    
                    $experience->setPhoto($newFilename);
                }
           

        
            $this->entityManager->persist($experience);
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
            // 'experiences' => $experiences,
            'experiences' => $experienceRepository->findAll(), 
            

        ]);
    }
}
