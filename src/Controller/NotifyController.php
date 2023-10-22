<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Params\NotificationParams;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();


        $query = $this->entityManager->getRepository(Notification::class)->findBy(
            [
                'person' => $user,
            ],
            [
                'added_at' => 'DESC',
            ]
        );

        $notifications = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('notify/index.html.twig', [
            'notifications' => $notifications,
            'user' => $user,
        ]);
    }


    #[Route('/redirect/{id}', name: 'app_redirect_notification')]
    public function new($id): Response
    {
        $notification = $this->entityManager->getRepository(Notification::class)->find($id);
        $news = $notification->getNews();
        $newsid = $news->getId();
        $notification->setIsRead(true);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
        if ($notification->getContent() === NotificationParams::NOT_TIMEGIVENFOREDIT) {
            return $this->redirectToRoute($notification->getDestination() . $newsid);

        } elseif ($notification->getContent() === NotificationParams::NOT_SENDFOREDIT) {
            return $this->redirectToRoute($notification->getDestination() . $newsid);

        } elseif ($notification->getContent() === NotificationParams::NOT_PUBLISHED) {
            return $this->redirectToRoute($notification->getDestination() . $newsid);

        } else
            return $this->redirectToRoute($notification->getDestination());

    }

}
