<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Chat;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/message')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'app_message_index', methods: ['GET'])]
    public function index(MessageRepository $messageRepository): Response
    {
        return $this->render('message/index.html.twig', [
            'messages' => $messageRepository->findAll(),
        ]);
    }

    #[Route('/new/{chat_id}', name: 'app_message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Chat $chat_id, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        
        if ($request->isMethod('POST')) {
            $message->setChat_id($chat_id);
            $message->setContenu($request->request->get('contenu'));
            $message->setType($request->request->get('type', 'QUESTION'));
            $message->setPost_time(new \DateTime());
            // Assuming you have a way to get the current user
            // $message->setPosted_by($this->getUser());

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_chat_show', ['id' => $chat_id->getId()]);
        }

        return $this->render('message/new.html.twig', [
            'message' => $message,
            'chat' => $chat_id,
        ]);
    }

    #[Route('/{id}', name: 'app_message_show', methods: ['GET'])]
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $message->setContenu($request->request->get('contenu'));
            $message->setType($request->request->get('type'));

            $entityManager->flush();

            return $this->redirectToRoute('app_chat_show', ['id' => $message->getChat_id()->getId()]);
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/{id}', name: 'app_message_delete', methods: ['POST'])]
    public function delete(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        $chatId = $message->getChat_id()->getId();
        
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chat_show', ['id' => $chatId]);
    }
} 