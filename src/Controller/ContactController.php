<?php
    
namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ContactMessage;
use App\Form\ContactMessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
   

    #[Route('/contacter', name: 'contacter')]
    public function contact(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $contactMessage = new ContactMessage();
        $form = $this->createForm(ContactMessageType::class, $contactMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer le message de contact dans la base de données
            // $entityManager = $this->$entityManager->getRepository(ContactMessageType::class);
            $entityManager->persist($contactMessage);
            $entityManager->flush();

            // Envoyer le message par e-mail
            $email = (new Email())
                ->from($contactMessage->getEmail())
                ->to('daouda.camara@atlassoon.fr') 
                ->subject($contactMessage->getSubject())
                ->html(
                    $this->renderView(
                        'emails/contact_message.html.twig',
                        ['contactMessage' => $contactMessage]
                    )
                );



            $mailer->send($email);


            $mail = new Mail();
        $content = "Bonjour " . $contactMessage->getNom() . " ". $contactMessage->getPrenom() . "<br/>

        Nous avons bien reçu votre mail concernant : <br><br><br>" . $contactMessage->getSubject() . "<br/> 
<br>Nous allons traiter votre mail et vous apporter une réponse dans les plus bref délai afin que nous puissions vous fournir une explication détaillée.
<br>
Par ailleurs, nous vous invitons à consulter notre site web : www.daouda-camara.fr pour découvrir d'autres informations.

Si vous avez des questions ou besoin d'assistance supplémentaire, n'hésitez pas à nous contacter. Nous sommes là pour vous aider.

En attendant, n'hésitez pas à parcourir notre catalogue en ligne pour découvrir des besoins auxquels nous pourrions vous accompagner. Si vous avez des questions ou des demandes spécifiques, n'hésitez pas à nous contacter à tout moment.

Nous vous remercions encore pour votre patience et votre compréhension.
        
<br>Daily QHSE  <br/>";
$mail->send($contactMessage->getEmail(), $contactMessage->getNom(). ' '. $contactMessage->getPrenom()  , "Confirmation de votre mail de contact", $content);

// ecrire un mail à l'administrateur en cas de mail envoyé par un utilisater :

$mailadmin = new Mail();
$contentadmin = "Bonjour CAMARA Daouda,  ". "<br/>

Vous venez de recevoir un mail de " .$contactMessage->getNom(). ' '. $contactMessage->getPrenom() . " <br> Veiller consuler vos mails d'administration afin de traiter sa demande. 

<br>Daily QHSE <br/>";
$mailadmin->send('daoudasouleymanecamara8@gmail.com', 'Daouda', 'Nouveau contact par Messagerie de contact', $contentadmin);
$notification = "Confirmation DAILY QHSE";

            $this->addFlash('success', 'Votre message a bien été envoyé !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

