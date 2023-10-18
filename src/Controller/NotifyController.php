<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotifyController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/notify', name: 'app_notify')]
    public function index(): Response
    {
        $user = $this->getUser();
        $Notification = $this->entityManager->getRepository(Notification::class)->findBy([
            'author'=> $user
        ]);

        return $this->render('notify/index.html.twig', [
            'notifications' => $Notification,
        ]);
    }
}
