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

    #[Route("/experience-detail/{id}", name:'detail_experience')]

    public function details(Experience $experience): Response
    {
        return $this->render('experiences/detail.html.twig', [
            'experience' => $experience,

        ]);
    }

    #[Route("/experience/modifier/{id}", name: "modifier_experience")]
   



    public function edit(Request $request, experience $experience, EntityManagerInterface $entityManager): Response
    {
        // Créer le formulaire et lier l'entité experience à celui-ci
        $form = $this->createForm(experiencesType::class, $experience);
        
        // Gérer la requête
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour l'experience en base de données
            $entityManager->flush();

            // Rediriger ou afficher un message de succès
            return $this->redirectToRoute('experience_show', ['id' => $experience->getId()]);
        }

    return $this->render('experiences/modifier.html.twig', [
        'form' => $form->createView(),
        'experience' => $experience,
    ]);
}

#[Route("/experience/supprimer/{id}", name: "supprimer_experience")]
public function supprimerexperience(Experience $experience, EntityManagerInterface $entityManager): Response
{
    // Vérifier si l'utilisateur connecté est un administrateur
    if (!$this->isGranted('ROLE_ADMIN')) {
      
        
        // return $this->redirectToRoute('app_listes_experiences');
        $entityManager->remove($experience);
        $entityManager->flush();

        $this->addFlash('success', 'L\'experience a été supprimé avec succès.');
        
    }
    
    




    return $this->redirectToRoute('show_experience');
}

}
