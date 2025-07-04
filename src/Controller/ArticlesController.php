<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticlesType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
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
            'articlesJour' => 'articles du jour par Daouda',
        ]);
    }
    #[Route('ajout_article', name:'app_ajout-article')]
    public function AddArticle(Request $request, SluggerInterface $slugger,  MailerInterface $mailer,) : Response 
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
            // mail de validation article ajout :
         // Envoyer le message par e-mail
         $email = (new Email())
         ->from('daouda.camara.040319@gmail.com')
        //  ->from($article->getEmail())
         ->to('daouda.camara@atlassoon.fr') 
         ->subject($article->getTitre())
         ->html(
             $this->renderView(
                 'emails/article-email.html.twig',
                 
             )
         );



     $mailer->send($email);


     $mail = new Mail();
 $content = "Bonjour Daouda" . $article->getUser()->getNom() . " ". $article->getUser()->getPrenom() . "<br/>


 Nous avons bien reçu votre mail concernant : <br><br><br>" . $article->getTitre() . "<br/> 
<br>Votre article ajouté dans notre plateforme Blog Daily QHSE et nous vous remercions de votre contribution.
<br>
Par ailleurs, nous vous invitons à consulter notre site web : www.daouda-camara.fr pour découvrir d'autres informations.

Si vous avez des questions ou besoin d'assistance supplémentaire, n'hésitez pas à nous contacter. Nous sommes là pour vous aider.

En attendant, n'hésitez pas à parcourir notre catalogue en ligne pour découvrir des besoins auxquels nous pourrions vous accompagner. Si vous avez des questions ou des demandes spécifiques, n'hésitez pas à nous contacter à tout moment.

Nous vous remercions encore pour votre patience et votre compréhension.
 
<br>Daily QHSE  <br/>";
$mail->send($article->getUser()->getEmail(), $article->getUser()->getNom(). ' '. $article->getUser()->getPrenom()  , "Confirmation de votre mail de contact", $content);

// ecrire un mail à l'administrateur en cas de mail envoyé par un utilisater :

$mailadmin = new Mail();
$contentadmin = "Bonjour CAMARA Daouda,  ". "<br/>

Vous venez de recevoir un mail de " .$article->getUser()->getNom(). ' '. $article->getUser()->getPrenom() . " <br> Veiller consuler vos mails d'administration afin de traiter sa demande. 

<br>Daily QHSE <br/>";
$mailadmin->send('daoudasouleymanecamara8@gmail.com', 'Daouda', 'Un nouveau article ajouté !!! 😍', $contentadmin);
$notification = "Confirmation DAILY QHSE";

     $this->addFlash('success', 'Votre article a bien été envoyé !');

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

    #[Route("/article/supprimer/{id}", name: "supprimer_article")]
    public function supprimerArticle(Article $article, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur connecté est un administrateur
        if (!$this->isGranted('ROLE_ADMIN')) {
          
            
            // return $this->redirectToRoute('app_listes_articles');
            $entityManager->remove($article);
            $entityManager->flush();
    
            $this->addFlash('success', 'L\'article a été supprimé avec succès.');
            
        }
        
        




        return $this->redirectToRoute('app_listes_articles');
    }

 

}
