<?php

namespace App\Controller;

use App\Entity\Notification;
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
        $query = $this->entityManager->getRepository(Notification::class)->findBy([
            'author'=> $user, 'notifications' => '0'
        ],[
            'date_at' => 'DESC'
            ]);
        $notifications = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        $query1 = $this->entityManager->getRepository(Notification::class)->findBy([
            'editor' => $user,
            'notifications' => 2,
        ], [
            'date_at' => 'DESC'
        ]);

        $query2 = $this->entityManager->getRepository(Notification::class)->findBy([
            'status' => 'NewAdd',
        ], [
            'date_at' => 'DESC'
        ]);

        $issue = array_merge($query1, $query2);

        $editor = $paginator->paginate(
            $issue,
            $request->query->getInt('page', 1),
            10
        );


        $resetcount = $this->entityManager->getRepository(Notification::class)->findBy([
            'count' => 1,
        ]);

        if (!empty($resetcount)) {
            foreach ($resetcount as $notification) {
                $notification->setCount(0);
                $this->entityManager->persist($notification);
            }

            $this->entityManager->flush();
        }


        return $this->render('notify/index.html.twig', [
            'notifications' => $notifications,
            'user' => $user,
            'editors' => $editor,
        ]);
    }
}
