<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class TestResetEmailCommand extends Command
{
    protected static $defaultName = 'app:test-reset-email';

    protected function configure(): void
    {
        $this->setDescription('Test password reset email sending with Mailtrap');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('Creating Mailtrap transport...');
            
            // Create DSN and transport
            $dsn = 'smtp://4f862822e8295a:826967966bef0f@sandbox.smtp.mailtrap.io:2525?encryption=tls';
            $transport = Transport::fromDsn($dsn);
            
            $output->writeln('Transport created successfully');
            
            // Create mailer
            $mailer = new Mailer($transport);
            
            $output->writeln('Creating test reset password email...');
            
            // Create a test reset password email
            $email = (new Email())
                ->from(new Address('ttttfarah@gmail.com', 'HACKIFY'))
                ->to('ttttfarah@gmail.com')
                ->subject('Test - Réinitialisation de votre mot de passe HACKIFY')
                ->text('Cliquez sur ce lien pour réinitialiser votre mot de passe: http://localhost:8000/reset-test')
                ->html('<p>Cliquez sur ce lien pour réinitialiser votre mot de passe: <a href="http://localhost:8000/reset-test">Réinitialiser le mot de passe</a></p>')
                ->priority(Email::PRIORITY_HIGH);
            
            $output->writeln('Sending email...');
            
            // Send the email
            $mailer->send($email);
            
            $output->writeln('Email sent successfully!');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
            $output->writeln('File: ' . $e->getFile());
            $output->writeln('Line: ' . $e->getLine());
            $output->writeln('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
