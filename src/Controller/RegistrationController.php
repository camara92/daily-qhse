<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $notification = null; 
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )

            );
            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'photo' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();
    
                // Move the file to the directory where images are stored
                try {
                    $photo->move(
                        $this->getParameter('image_directory'),
                        // image directory dans service yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
    
                // updates the 'imageFilename' property to store the PDF file name
                // instead of its contents
                $user->setPhoto($newFilename);
            }

            $entityManager->persist($user);
            $entityManager->flush();
            // notifier l'inscription de l'utilisateur comme un envoi de notifi 

            $mail = new Mail(); 
            $content = "Bonjour " .$user->getNom() . " " .$user->getPrenom().",". "<br/>Bienvenue dans votre plateforme en ressource sur la Qualité, Sécurité, hygiène et Environnement. De l'ingénierie HQSE, CSPS et dans la construction et aussi en industrie pour tous. 
             <br/>
             <p> N'hésitez pas poser des questions, créer des articles ou participer aux forums de discussions pour nous apporter tes connaissances. 
             </p>
             
             ";
            $mail->send($user->getEmail(), $user->getNom(), "Inscription validée sur Daily QHSE", $content); 
             $notification="Votre inscription a été enregistrée. "; 

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'notification'=>$notification
        ]);
    }
}
