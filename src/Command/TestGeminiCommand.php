<?php

namespace App\Command;

use App\Service\GeminiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:test-gemini')]
class TestGeminiCommand extends Command
{
  public function __construct(private GeminiService $geminiService)
  {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this
      ->addArgument('prompt', InputArgument::REQUIRED, 'The prompt to send to Gemini')
      ->setDescription('Test Gemini API integration');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $prompt = $input->getArgument('prompt');

    try {
      $response = $this->geminiService->generateContent($prompt);
      $output->writeln($response);
      return Command::SUCCESS;
    } catch (\Exception $e) {
      $output->writeln('Error: ' . $e->getMessage());
      return Command::FAILURE;
    }
  }
}
