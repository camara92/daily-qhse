<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Entity\ResetPassword;
use App\Form\ResetPassType;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class ResetPasswordController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

   
    #[Route("/reset-password", name:"reset_password")]
    
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($request->get('email')) {
         
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));
          
            if ($user) {
              
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTimeImmutable());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();
                // envoie email pour réinitialisation 

                $url = $this->generateUrl('reset_password_edit', ['token' => $reset_password->getToken()]);
                $content = "Bonjour " . $user->getNom() . "<br/>Votre demande de réinitialisation de votre mot de passe. <br/><br/>";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant <a href='http://127.0.0.1:8000/" . $url . "'>le lien</a> afin de mettre à jour votre mot de passe. ";
                $email = new Mail();

                $email->send($user->getEmail(), $user->getNom() . ' ' . $user->getPrenom(), "Réinitialisation de votre mot de passe ", $content);
                $this->addFlash("Notice", "Vous allez recevoir un mail pour la réinitialisation de votre mot de passe. Merci de vérifier votre mail et vos spams. ");
            
            } else {
                $this->addFlash("Notice", "Le mail renseigné est inconnu. Merci de vérifier votre mail. ");
            }
        }
        return $this->render('reset_password/index.html.twig', []);
    }

 
     #[Route("/reset-password_edit/{token}", name:"reset_password_edit")]
   
    public function UpdatePassword($token, Request $request,UserPasswordHasherInterface  $passwordHasher)
    {
      
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);
        if (!$reset_password) {
            return $this->redirectToRoute('reset_password');
        } else {
            // we verify if createdAt = now - 2h or plus 
            $now = new \DateTime();
            if ($now > $reset_password->getCreatedAt()->modify(' +3 hour')) {
               
                $this->addFlash("Notice", "Votre demande de mot de passe a expiré. Merci de renouveller la demande de réinitialisation de votre mot de passe. ");
                return $this->redirectToRoute('reset_password');
            }
            
            $form = $this->createForm(ResetPassType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));
                $usere = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('Nom'));
                
                $new_password = $form->get("new_password")->getData();
             
                $new_password = $form->get('new_password')->getData();
                // dd($new_password);
                $password = $passwordHasher->hashPassword ($reset_password->getUser(), $new_password);
                $reset_password->getUser()->setPassword($password);
                $this->entityManager->flush();

//                 $content = "Bonjour, 
//                 <br/>
//                 Nous vous informons que votre mot de passe a été modifié avec succès.

// Si vous n'avez pas effectué cette modification ou si vous pensez que votre compte a été compromis, veuillez nous contacter immédiatement à daoudasouleymanecamara8@gmail.com pour une assistance supplémentaire.

// Merci de votre confiance et de votre collaboration.

// Cordialement,
//                 <br/>";
              
//                 $email = new Mail();

//                 $email->send($user->getEmail(), $user->getNom() . ' ' . $user->getPrenom(), "Modificaton de votre mot de passe confirmée ", $content);
                $this->addFlash("Notice", "Votre mot de passe a bien été modifié avec succès. ");
                
                return $this->redirectToRoute('app_login');
            }
            $this->addFlash("success", "Votre mot de passe a bien été modifié. ");
            // redirect vers some page 


        }

        return $this->render('reset_password/updatepassword.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
