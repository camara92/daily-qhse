<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticlesType;
use App\Repository\ArticleRepository;
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

    // les listes 
    #[Route('listes_article', name:'app_listes_articles')]
    public function listesArticles(ArticleRepository $articleRepository): Response {

        return $this->render('articles/listes.article.html.twig', [
            'listes_articles' => $articleRepository->findAll(),
            
            
        ]);
    }
  

    #[Route("/detail/{id}", name:'detail_article')]

    public function details(Article $article): Response
    {
        return $this->render('articles/detail.html.twig', [
            'article' => $article,

        ]);
    }

    #[Route("/article/modifier/{id}", name: "modifier_article")]
   



        public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
        {
            // Créer le formulaire et lier l'entité Article à celui-ci
            $form = $this->createForm(ArticlesType::class, $article);
            
            // Gérer la requête
            $form->handleRequest($request);
    
            // Vérifier si le formulaire est soumis et valide
            if ($form->isSubmitted() && $form->isValid()) {
                // Mettre à jour l'article en base de données
                $entityManager->flush();
    
                // Rediriger ou afficher un message de succès
                return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
            }

        return $this->render('articles/modifier.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    

 

}
