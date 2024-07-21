<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticlesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticlesController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/articles', name: 'app_articles')]
    public function index(): Response
    {
        return $this->render('articles/index.html.twig', [
            'controller_name' => 'ArticlesController',
        ]);
    }
    #[Route('ajout_article', name:'app_ajout-article')]
    public function AddArticle(Request $request, SluggerInterface $slugger) : Response 
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $personne_article = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $user]);
        $article = new Article();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new \DateTimeImmutable());
            $article->setUser($personne_article);
        //    Partie Media 
         // Enregistrer article dans la base de données
         $photo = $form->get('imagesReferences')->getData();

         if ($photo) {
             $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
             // Cela est nécessaire pour inclure en toute sécurité le nom du fichier dans l'URL.
             $safeFilename = $slugger->slug($originalFilename);
             $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();
             // stockage de média dans partie public et service yaml :
             try {
                 $photo->move(
                     $this->getParameter('image_articles'),
                     // image directory dans service yaml
                     $newFilename
                 );
             } catch (FileException $e) {
                //  information confirmation ajout media ? lol 
             }
 
             
             $article->setImagesReferences($newFilename);
         }
            $this->entityManager->persist($article);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('articles/ajouter.article.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }
}
