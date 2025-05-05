<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class TestEmailCommand extends Command
{
    protected static $defaultName = 'app:test-email';
    protected static $defaultDescription = 'Test email configuration';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('Testing email configuration...');
            
            // Create transport
            $dsn = 'smtp://ttttfarah%40gmail.com:pjzp%20fqie%20epci%20wkmk@smtp.gmail.com:587?encryption=tls&auth_mode=login';
            $output->writeln('Using DSN: ' . $dsn);
            $transport = Transport::fromDsn($dsn);
            $mailer = new Mailer($transport);
            
            $output->writeln('Created transport and mailer');
            
            // Create a test email
            $email = (new Email())
                ->from(new Address('ttttfarah@gmail.com', 'HACKIFY'))
                ->to('ttttfarah@gmail.com')
                ->subject('Test Email from HACKIFY')
                ->text('This is a test email to verify the email configuration.')
                ->html('<p>This is a test email to verify the email configuration.</p>');
            
            $output->writeln('Sending test email...');
            
            // Send the email
            $mailer->send($email);
            
            $output->writeln('Test email sent successfully!');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            $output->writeln('Stack trace:');
            $output->writeln($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
