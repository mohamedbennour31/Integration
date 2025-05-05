<?php

namespace App\Controller;

use App\Entity\Communaute;
use App\Entity\Chat;
use App\Entity\User;
use App\Repository\CommunauteRepository;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/communaute')]
class CommunauteController extends AbstractController
{
    #[Route('/', name: 'app_communaute_index', methods: ['GET'])]
    public function index(CommunauteRepository $communauteRepository): Response
    {
        return $this->render('communaute/index.html.twig', [
            'communaute' => $communauteRepository->findAll(),
        ]);
    }

    #[Route('/organisateur', name: 'app_communaute_organisateur', methods: ['GET'])]
    public function getOrganisatorCommunities(EntityManagerInterface $entityManager): Response
    {
        // Get the current user
        $user = $this->getUser();
        if (!$user || !in_array('ROLE_ORGANISATEUR', $user->getRoles())) {
            throw $this->createAccessDeniedException('Accès refusé. Vous devez être un organisateur.');
        }
        
        // Find all communities linked to hackathons created by this organizer
        $query = $entityManager->createQuery(
            'SELECT c, h 
            FROM App\Entity\Communaute c
            JOIN c.id_hackathon h
            WHERE h.id_organisateur = :user'
        )->setParameter('user', $user);
        
        $communities = $query->getResult();
        
        return $this->json([
            'communities' => $communities
        ]);
    }

    #[Route('/participant', name: 'app_communaute_participant', methods: ['GET'])]
    public function getParticipantCommunities(EntityManagerInterface $entityManager, ParticipationRepository $participationRepository): Response
    {
        // Get the current user
        $user = $this->getUser();
        if (!$user || !in_array('ROLE_PARTICIPANT', $user->getRoles())) {
            throw $this->createAccessDeniedException('Accès refusé. Vous devez être un participant.');
        }
        
        // Find all the hackathons the user is participating in
        $participations = $participationRepository->findBy(['participant' => $user]);
        
        // Extract the hackathon IDs
        $hackathonIds = [];
        foreach ($participations as $participation) {
            $hackathonIds[] = $participation->getHackathon()->getId_hackathon();
        }
        
        // Find all communities linked to these hackathons
        $query = $entityManager->createQuery(
            'SELECT c, h 
            FROM App\Entity\Communaute c
            JOIN c.id_hackathon h
            WHERE h.id_hackathon IN (:hackathonIds)'
        )->setParameter('hackathonIds', $hackathonIds);
        
        $communities = $query->getResult();
        
        return $this->json([
            'communities' => $communities
        ]);
    }

    #[Route('/mes-communautes', name: 'app_communaute_mes_communautes', methods: ['GET'])]
    public function getMyCommunities(EntityManagerInterface $entityManager, ParticipationRepository $participationRepository): Response
    {
        // Get the current user
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }
        
        $communities = [];
        
        // If user is an organizer, get communities for their hackathons
        if (in_array('ROLE_ORGANISATEUR', $user->getRoles())) {
            $query = $entityManager->createQuery(
                'SELECT c, h 
                FROM App\Entity\Communaute c
                JOIN c.id_hackathon h
                WHERE h.id_organisateur = :user'
            )->setParameter('user', $user);
            
            $organizerCommunities = $query->getResult();
            $communities = array_merge($communities, $organizerCommunities);
        }
        
        // If user is a participant, get communities for hackathons they're participating in
        if (in_array('ROLE_PARTICIPANT', $user->getRoles())) {
            $participations = $participationRepository->findBy(['participant' => $user]);
            
            $hackathonIds = [];
            foreach ($participations as $participation) {
                $hackathonIds[] = $participation->getHackathon()->getId_hackathon();
            }
            
            if (!empty($hackathonIds)) {
                $query = $entityManager->createQuery(
                    'SELECT c, h 
                    FROM App\Entity\Communaute c
                    JOIN c.id_hackathon h
                    WHERE h.id_hackathon IN (:hackathonIds)'
                )->setParameter('hackathonIds', $hackathonIds);
                
                $participantCommunities = $query->getResult();
                $communities = array_merge($communities, $participantCommunities);
            }
        }
        
        // Filter out duplicates
        $uniqueCommunities = [];
        $ids = [];
        foreach ($communities as $community) {
            if (!in_array($community->getId(), $ids)) {
                $ids[] = $community->getId();
                $uniqueCommunities[] = $community;
            }
        }
        
        return $this->render('communaute/mes_communautes.html.twig', [
            'communities' => $uniqueCommunities
        ]);
    }

    #[Route('/new', name: 'app_communaute_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $communaute = new Communaute();
        
        if ($request->isMethod('POST')) {
            $communaute->setId_hackathon($request->request->get('id_hackathon'));
            $communaute->setNom($request->request->get('nom'));
            $communaute->setDescription($request->request->get('description'));
            $communaute->setDate_creation(new \DateTime());

            $entityManager->persist($communaute);
            $entityManager->flush();

            // Create 5 chats for the community
            $chatTypes = ['ANNOUNCEMENT', 'QUESTION', 'FEEDBACK', 'COACH', 'BOT_SUPPORT'];
            $chatNames = [
                'Announcements',
                'Questions',
                'Feedback',
                'Coaching',
                'Bot Support'
            ];

            foreach ($chatTypes as $index => $type) {
                $chat = new Chat();
                $chat->setCommunaute_id($communaute);
                $chat->setNom($chatNames[$index]);
                $chat->setType($type);
                $chat->setDate_creation(new \DateTime());
                
                $entityManager->persist($chat);
            }
            
            $entityManager->flush();

            return $this->redirectToRoute('app_communaute_index');
        }

        return $this->render('communaute/new.html.twig', [
            'communaute' => $communaute,
        ]);
    }

    #[Route('/{id}', name: 'app_communaute_show', methods: ['GET'])]
    public function show(Communaute $communaute): Response
    {
        return $this->render('communaute/show.html.twig', [
            'communaute' => $communaute,
            'chats' => $communaute->getChats(),
        ]);
    }

    
} 