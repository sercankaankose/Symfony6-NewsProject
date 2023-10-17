<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotifyController extends AbstractController
{
    #[Route('/notify', name: 'app_notify')]
    public function index(): Response
    {
        return $this->render('notify/index.html.twig', [
            'controller_name' => 'NotifyController',
        ]);
    }
}
