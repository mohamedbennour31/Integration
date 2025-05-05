<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestMailerCommand extends Command
{
    protected static $defaultName = 'app:test-mailer';
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this->setDescription('Test the mailer configuration');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('Current MAILER_DSN: ' . $_ENV['MAILER_DSN']);
            $output->writeln('Creating email object...');
            
            $email = (new Email())
                ->from('ttttfarah@gmail.com')
                ->to('ttttfarah@gmail.com')
                ->subject('Test email from Hackify - ' . date('Y-m-d H:i:s'))
                ->text('This is a test email to verify the mailer configuration.');

            $output->writeln('Email object created successfully');
            $output->writeln('From: ttttfarah@gmail.com');
            $output->writeln('To: ttttfarah@gmail.com');
            
            $output->writeln('Attempting to send test email...');
            $this->mailer->send($email);
            $output->writeln('Test email sent successfully!');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('\nError Details:');
            $output->writeln('Message: ' . $e->getMessage());
            $output->writeln('File: ' . $e->getFile());
            $output->writeln('Line: ' . $e->getLine());
            $output->writeln('\nStack trace:\n' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
