<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Services\MailerService;

class SendEmailCommand extends Command
{
    private $mailerService;

    public function __construct(MailerService $mailerService)
    {
        parent::__construct();
        $this->mailerService = $mailerService;
    }

    protected function configure()
    {
        $this
            ->setName('app:send-email')
            ->setDescription('Send an email with the provided text')
            ->addArgument('text', InputArgument::REQUIRED, 'The text to include in the email');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $text = $input->getArgument('text');
        $this->mailerService->mandaemail( $text, "Mail desde el comando", "jmrg00021@gmail.com");

        $output->writeln('Email sent successfully.');

        // Devuelve un código de salida
        return Command::SUCCESS;
    }
}
