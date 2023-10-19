<?php

namespace App\Controller;

use App\Entity\Notification;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FlashCounterService;


class FunctionController extends AbstractController
{
    private  $UserRepository;
    private  $newsRepository;
    private $session;
    private EntityManagerInterface $entityManager;

    public function __construct(RequestStack $requestStack, UserRepository $UserRepository, NewsRepository $newsRepository, EntityManagerInterface $entityManager)
    {
        $this->session = $requestStack->getSession();
        $this->UserRepository = $UserRepository;
        $this->newsRepository = $newsRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/notify', name: 'app_notify')]
    public function index(): Response
    {
     //   $flashCounter = $this->getFlashCounter();

        return $this->render('notify/index.html.twig', [
            'controller_name' => 'NotifyController',
        ]);
    }

    #[Route('/news/add', name: 'app_news')]
    public function addNews(Request $request): Response
    {
        $user = $this->getUser();

        if ($user && !$user->isVerified()) {
            return $this->render('please-verify-email.html.twig', [
                'user' => $user,
            ]);
        }

        $addNews = new News();
        $form = $this->createForm(NewsType::class, $addNews);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addNews->setAuthor($user);
            $addNews->setEditor(null);

            $title = $form->get('title')->getData();
            $addNews->setTitle($title);
            $addNews->setStatus('waiting');


            $now = new DateTimeImmutable();
            $addNews->setCreatedAt($now);

            $newsImage = $form->get('image')->getData();
            if ($newsImage) {
                $newFilename = uniqid() . '.' . $newsImage->guessExtension();

                try {
                    $newsImage->move(
                        $this->getParameter('kernel.project_dir') . '/public/news',
                        $newFilename
                    );
                    $addNews->setImage('/news/' . $newFilename);
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
            }
            $this->entityManager->persist($addNews);
            $this->entityManager->flush();

            $now = new DateTime();
            $notify = new Notification();
            $notify->setNews($addNews);
            $notify->setStatus('NewAdd');
            $notify->setDateAt($now);
            $notify->setAuthor($addNews->getAuthor());
            $notify->setNotifications(2);
            $this->entityManager->persist($notify);
            $this->entityManager->flush();

            $this->addFlash(
                'newnews',
                'News has been sent to the Editor.'
            );



//            $notificationMessage = 'New news completed successfully: ';
//            $this->addFlash('notice', $notificationMessage);
//            $this->flashCounterService->incrementFlashCounter();

            return $this->redirectToRoute('app_news');
        }


        return $this->render('news/addnews.html.twig', [
            'form' => $form->createView(),
        ]);


    }


}