<?php

namespace App\Controller;

use App\Entity\Participation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Hackathon;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\TwilioService;

final class ParticipationController extends AbstractController
{
    private $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    #[Route('/hackathons/calendar', name: 'hackathons_calendar')]
    public function calendar(EntityManagerInterface $em): Response
    {
        $hackathons = $em->getRepository(Hackathon::class)->findAll();
        
        return $this->render('hackathon/calendar.html.twig', [
            'hackathons' => $hackathons
        ]);
    }
    #[Route('hackathon/{id}/participer', name: 'hackathon_participer')]
    public function participer(
        Hackathon $hackathon,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        $participation = new Participation();
        $participation->setHackathon($hackathon);
        $participation->setDate_inscription(new \DateTime());
        $participation->setStatut('En attente');
        $participation->setParticipant($user);
    
        $em->persist($participation);
        $em->flush();
    
        // ✅ Flash message
        $this->addFlash('success', 'Votre demande de participation au hackathon "' . $hackathon->getNomHackathon() . '" a bien été envoyée. Vous recevrez une confirmation prochainement.');
    
        // ✅ Envoi SMS
        $participantPhone = '+216' . $user->getTelUser(); // Assure-toi que getTelUser() existe
        $message = "📩 Bonjour ! Votre demande de participation au hackathon \"" . $hackathon->getNomHackathon() . "\" a bien été enregistrée. Statut : En attente. Merci pour votre inscription !";
    
        $this->twilioService->sendSms($participantPhone, $message);
    
        return $this->redirectToRoute('hackathon_details', ['id' => $hackathon->getId_hackathon()]);
    }
    
#[Route('hackathon/{id}/participants', name: 'voir_participants')]
public function voirParticipants(Hackathon $hackathon, ParticipationRepository $participationRepository): Response
{
    $participations = $participationRepository->findBy(['hackathon' => $hackathon]);

    return $this->render('participation/afficherParticipation.html.twig', [
        'hackathon' => $hackathon,
        'participations' => $participations,
    ]);
}
#[Route('participation/annuler/{id}', name: 'annuler_participation')]
public function annulerParticipation(
    Participation $participation,
    EntityManagerInterface $entityManager
): Response {
    $entityManager->remove($participation);
    $entityManager->flush();

    return $this->redirectToRoute('liste_hackathon'); 
}
#[Route('/participation/{id}/valider', name: 'valider_participation')]
public function accepter(Participation $participation, EntityManagerInterface $entityManager): Response
{
    // Met à jour le statut de la participation
    $participation->setStatut('validé');
    $entityManager->flush();

    // Récupération du téléphone et du nom du hackathon
    $participantPhone = '+216' . $participation->getParticipant()->getTelUser();
    $hackathonName = $participation->getHackathon()->getNomHackathon();

    // Message SMS personnalisé
    $message = "🎉 Félicitations ! Votre participation au hackathon \"$hackathonName\" a été *validée*. Préparez-vous à vivre une belle expérience !";

    // Envoie du SMS
    $this->twilioService->sendSms($participantPhone, $message);

    return $this->redirectToRoute('voir_participants', ['id' => $participation->getHackathon()->getId_hackathon()]);
}

#[Route('/participation/{id}/refuser', name: 'refuser_participation')]
public function refuser(Participation $participation, EntityManagerInterface $entityManager): Response
{
    // Met à jour le statut
    $participation->setStatut('refusé');
    $entityManager->flush();

    // Récupération du téléphone et du nom du hackathon
    $participantPhone = '+216' . $participation->getParticipant()->getTelUser();
    $hackathonName = $participation->getHackathon()->getNomHackathon();

    // Message SMS personnalisé
    $message = "❌ Nous sommes désolés. Votre participation au hackathon \"$hackathonName\" a été *refusée*. Nous espérons vous voir à nos prochains événements.";

    // Envoie du SMS
    $this->twilioService->sendSms($participantPhone, $message);

    return $this->redirectToRoute('voir_participants', ['id' => $participation->getHackathon()->getId_hackathon()]);
}

}
