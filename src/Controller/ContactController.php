<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Form\BookingType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact_form')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // Send email
            $email = (new Email())
                ->from($data['email'])
                ->to('your-email@example.com')
                ->subject($data['subject'])
                ->text($data['message']);

            try {
                $mailer->send($email);
                $this->addFlash('success', 'Votre message a été envoyé avec succès!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi du message.');
            }

            return $this->redirectToRoute('contact_form');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/booking', name: 'booking_form')]
    public function booking(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(BookingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // Send confirmation email
            $email = (new Email())
                ->from('your-email@example.com')
                ->to($data['email'])
                ->subject('Confirmation de réservation')
                ->text(sprintf(
                    "Votre réservation a été reçue pour %d personnes le %s à %s.\n\nMessage: %s",
                    $data['people'],
                    $data['date']->format('Y-m-d'),
                    $data['time']->format('H:i'),
                    $data['message'] ?? 'Aucun message'
                ));

            try {
                $mailer->send($email);
                $this->addFlash('success', 'Votre réservation a été envoyée avec succès!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de la réservation.');
            }

            return $this->redirectToRoute('booking_form');
        }

        return $this->render('booking/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
