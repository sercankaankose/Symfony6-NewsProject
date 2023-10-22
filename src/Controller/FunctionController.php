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

//    #[Route('/notify', name: 'app_notify')]
//    public function index(): Response
//    {
//     //   $flashCounter = $this->getFlashCounter();
//
//        return $this->render('notify/index.html.twig', [
//            'controller_name' => 'NotifyController',
//        ]);
//    }




}