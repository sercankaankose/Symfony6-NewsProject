<?php

namespace App\Twig;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getNotificationCount', [$this, 'getNotificationCount']),
        ];
    }

    public function getNotificationCount()
    {


        $user = $this->security->getUser();
        $UnreadnotifyCount = $this->entityManager->getRepository(Notification::class)->count([
            'person' => $user,
            'is_read' => false,
        ]);


        return $UnreadnotifyCount;
    }
}
