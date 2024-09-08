<?php

namespace App\Controller;

use App\Entity\Services;
use App\Entity\User;
use App\Form\ServicesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServicesController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/services', name: 'app_services')]
    public function index(): Response
    {
        return $this->render('services/index.html.twig', [
            'controller_name' => 'ServicesController',
        ]);
    }

    #[Route('/ajoutservice', name: 'ajout_services')]
    public function ajouerService(Request $request) : Response 
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $Service = new Services();
        $form = $this->createForm(ServicesType::class, $Service);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
           
               
            $this->entityManager->persist($Service);
            $this->entityManager->flush();
            return $this->redirectToRoute('ajout_services');
         }

        

        
        return $this->render('services/ajouter.service.html.twig', [
            'service' => $form->createView(),
            
        ]);
    }
}
