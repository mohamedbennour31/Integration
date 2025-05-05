<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class TestMailtrapCommand extends Command
{
    protected static $defaultName = 'app:test-mailtrap';

    protected function configure(): void
    {
        $this->setDescription('Test email sending with Mailtrap');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('Creating Mailtrap transport...');
            
            // Create the Mailtrap transport
            $transport = new EsmtpTransport(
                'sandbox.smtp.mailtrap.io',
                2525,
                false
            );
            
            $transport->setUsername('4f862822e8295a');
            $transport->setPassword('826967966bef0f');
            
            $output->writeln('Transport created successfully');
            
            // Create the Mailer
            $mailer = new Mailer($transport);
            
            $output->writeln('Creating test email...');
            
            // Create a test email
            $email = (new Email())
                ->from('ttttfarah@gmail.com')
                ->to('ttttfarah@gmail.com')
                ->subject('Test Email from Mailtrap - ' . date('Y-m-d H:i:s'))
                ->text('This is a test email sent via Mailtrap.')
                ->html('<p>This is a <b>test email</b> sent via Mailtrap.</p>');
            
            $output->writeln('Sending email...');
            
            // Send the email
            $mailer->send($email);
            
            $output->writeln('Email sent successfully!');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
            $output->writeln('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
