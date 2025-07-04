<?php

namespace App\Controller;

use App\Entity\References;
use App\Form\ReferencesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ReferencesController extends AbstractController
{
    #[Route('/references', name: 'app_references')]
    public function index(): Response
    {
        return $this->render('references/index.html.twig', [
            'controller_name' => 'ReferencesController',
        ]);
    }

    #[Route('/reference-add', name: 'ajouter_reference')]
    public function register(Request $request,  EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $notification = null; 
        $reference = new References();
        $form = $this->createForm(ReferencesType::class, $reference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $photo = $form->get('photo')->getData();

            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-daouda-'.uniqid().'.'.$photo->guessExtension();
    
                // Move the file to the directory where images are stored
                try {
                    $photo->move(
                        $this->getParameter('image_directory'),
                        // image directory dans service yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // je peux aussi ajouter un alert niveau front au besoin 
                    // ... handle exception if something happens during file upload
                }
    
               
                $reference->setPhoto($newFilename);
            }

            $entityManager->persist($reference);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_home');
        }

        return $this->render('references/ajouter.reference.html.twig', [
            'reference' => $form->createView(),
            'notification'=>$notification
        ]);
    }
}
