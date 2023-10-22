<?php

namespace App\Command;

use App\Entity\Role;
use App\Entity\User;
use App\Params\RoleParams;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'SetUserRole',
    description: 'Set a user role by email',
)]
class RoleCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email to set the role for')
            ->setDescription('Set a user role by email')
            ->setHelp('This command sets a specific role for a user by email.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $userRepository = $this->entityManager->getRepository(User::class);
        $role = $this->entityManager->getRepository(Role::class)->findOneBy(['id' => RoleParams::ROLE_EDITOR]);

        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user) {
            $user->addRoles($role);
            $this->entityManager->flush();
            $output->writeln('Role updated for user with email: ' . $email);
        } else {
            $output->writeln('User with email ' . $email . ' not found.');
        }

        return Command::SUCCESS;
    }
}
