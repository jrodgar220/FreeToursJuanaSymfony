<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    private $entityManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface  $passwordEncoder)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this
            ->setName('app:create-user')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a new user.')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the new user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output):int
    {
        $helper = $this->getHelper('question');
        $username = $input->getArgument('username');
        $apellidos = $helper->ask($input, $output, new Question('Enter apellidos: '));
        $email = $helper->ask($input, $output, new Question('Enter email: '));
        $passwordQuestion = new Question('Enter password: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $passwordQuestion);
        $roles = $helper->ask($input, $output, new Question('Enter roles (comma separated): '));

        $user = new User();
        $user->setNombre($username);
        $user->setEmail($email);
        $user->setApellidos($apellidos);

        $user->setPassword(
            $this->passwordEncoder->hashPassword(
                $user,
                $password
            )
        );
        $user->setRoles(explode(',', $roles));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('User created successfully.');

        return Command::SUCCESS;
    }
}
