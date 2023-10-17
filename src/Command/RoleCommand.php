<?php

namespace App\Command;

use App\Entity\Role;
use App\Entity\User;
use App\Params\RoleParams;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'RoleCommand',
    description: 'roles change',
)]
class RoleCommand extends Command
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
       // $this->setName('app:update-roles');

    }


    protected function configure(): void
    {
        $this
            ->setDescription('Update user roles condition')
            ->setHelp('This command updates user roles .');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $role = $this->entityManager->getRepository(Role::class)->findOneBy(['id' => RoleParams::ROLE_EDITOR]);

        $users = $userRepository->findAll();

        foreach ($users as $user) {
            $email = $user->getEmail();
            if (strpos($email, '@yt.com') !== false) {
                $user->addRoles($role);
            }
        }

        $this->entityManager->flush();

        $output->writeln('User roles updated successfully.');

        return Command::SUCCESS;
    }
}
