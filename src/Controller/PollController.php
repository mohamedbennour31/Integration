<?php

namespace App\Controller;

use App\Entity\Poll;
use App\Entity\Chat;
use App\Repository\PollRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/poll')]
class PollController extends AbstractController
{
    #[Route('/', name: 'app_poll_index', methods: ['GET'])]
    public function index(PollRepository $pollRepository): Response
    {
        return $this->render('poll/index.html.twig', [
            'polls' => $pollRepository->findAll(),
        ]);
    }

    #[Route('/new/{chat_id}', name: 'app_poll_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Chat $chat_id, EntityManagerInterface $entityManager): Response
    {
        $poll = new Poll();
        
        if ($request->isMethod('POST')) {
            $poll->setChat_id($chat_id);
            $poll->setQuestion($request->request->get('question'));
            $poll->setCreated_at(new \DateTime());
            $poll->setIs_closed(false);
            // Assuming you have a way to get the current user
            // $poll->setCreated_by($this->getUser());

            $entityManager->persist($poll);
            $entityManager->flush();

            return $this->redirectToRoute('app_chat_show', ['id' => $chat_id->getId()]);
        }

        return $this->render('poll/new.html.twig', [
            'poll' => $poll,
            'chat' => $chat_id,
        ]);
    }

    #[Route('/{id}', name: 'app_poll_show', methods: ['GET'])]
    public function show(Poll $poll): Response
    {
        return $this->render('poll/show.html.twig', [
            'poll' => $poll,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_poll_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Poll $poll, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $poll->setQuestion($request->request->get('question'));
            $poll->setIs_closed($request->request->get('is_closed', false));

            $entityManager->flush();

            return $this->redirectToRoute('app_chat_show', ['id' => $poll->getChat_id()->getId()]);
        }

        return $this->render('poll/edit.html.twig', [
            'poll' => $poll,
        ]);
    }

    #[Route('/{id}', name: 'app_poll_delete', methods: ['POST'])]
    public function delete(Request $request, Poll $poll, EntityManagerInterface $entityManager): Response
    {
        $chatId = $poll->getChat_id()->getId();
        
        if ($this->isCsrfTokenValid('delete'.$poll->getId(), $request->request->get('_token'))) {
            $entityManager->remove($poll);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chat_show', ['id' => $chatId]);
    }
} 