<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\EmailType;

class MailController extends AbstractController
{
  #[Route('/send-email', name: 'send_email')]
  public function sendEmail(Request $request, MailerInterface $mailer): Response
  {
    $form = $this->createForm(EmailType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $data = $form->getData();

      try {
        $email = (new Email())
          // Set your default from address here
          ->from('hackathonmanagement8@gmail.com')
          ->to($data['recipient_email'])
          ->subject($data['subject'])
          ->text($data['body']);

        $mailer->send($email);
        $this->addFlash('success', 'Email sent successfully!');
      } catch (\Exception $e) {
        $this->addFlash('error', 'Error sending email: ' . $e->getMessage());
      }

      return $this->redirectToRoute('send_email');
    }

    return $this->render('evaluation/send_email.html.twig', [
      'form' => $form->createView()
    ]);
  }
}
