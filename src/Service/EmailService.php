<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Environment;

class EmailService
{
  public function __construct(
    private MailerInterface $mailer,
    private MessageBusInterface $bus,
    private Environment $twig
  ) {}

  public function sendEmail(
    string $to,
    string $subject,
    string $templatePath,
    array $context = [],
    bool $async = true
  ): void {
    // Render the email template
    $html = $this->twig->render($templatePath, $context);

    $email = (new Email())
      ->from('noreply@yourdomain.com')
      ->to($to)
      ->subject($subject)
      ->html($html);

    if ($async) {
      // Dispatch the message to be handled async
      $this->bus->dispatch(new \Symfony\Component\Mailer\Messenger\SendEmailMessage($email));
    } else {
      // Send immediately
      $this->mailer->send($email);
    }
  }
}
