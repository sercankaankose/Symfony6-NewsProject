<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ContentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class denemeController extends AbstractController
{

    public function __construct(UserRepository $UserRepository, EntityManagerInterface $entityManager)
    {
        $this->UserRepository = $UserRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/category', name: 'app_category')]
    public function category(): Response
    {
        // $categories = $contentRepository->findAll();

        return $this->render('category.html.twig', [
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        // $categories = $contentRepository->findAll();
        return $this->render('about.html.twig', [
//        'categories' => $categories,
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {

        // $categories = $contentRepository->findAll();

        return $this->render('contact.html.twig', [
//        'categories' => $categories,
        ]);
    }

    #[Route('/search', name: 'app_search')]
    public function search(): Response
    {

        // $categories = $contentRepository->findAll();

        return $this->render('search-result.html.twig', [
//        'categories' => $categories,
        ]);
    }



}