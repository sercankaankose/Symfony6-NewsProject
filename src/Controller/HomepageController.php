<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomepageController extends AbstractController
{
    private UserRepository $UserRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $UserRepository, EntityManagerInterface $entityManager)
    {
        $this->UserRepository = $UserRepository;
        $this->entityManager = $entityManager;
    }

    // #[IsGranted('ROLE_EDITOR')]
    #[Route('/', name: 'app_homepage')]
    public function index(Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();

        if ($user) {
            $user = $this->UserRepository->findOneBy(['id' => $user->getId()]);
        }
        $popularNews = $entityManager->getRepository(News::class)->findBy(
            ['status' => 'published'],
            ['view_count' => 'DESC'],
            8);
        $latestNews = $this->entityManager->getRepository(News::class)->findBy(
            ['status' => 'published'],
            ['published_at' => 'DESC'],
        5);
        $sportsNews = $this->entityManager->getRepository(News::class)->findBy(
            ['status' => 'published', 'category' => '1'],
            ['view_count' => 'DESC'],
            10
        );
        $politicsNews = $this->entityManager->getRepository(News::class)->findBy(
            ['status' => 'published', 'category' => '2'],
            ['view_count' => 'DESC'],
            10
        );

        $financeNews = $this->entityManager->getRepository(News::class)->findBy(
            ['status' => 'published', 'category' => '3'],
            ['view_count' => 'DESC'],
            10
        );

        $technologyNews = $this->entityManager->getRepository(News::class)->findBy(
            ['status' => 'published', 'category' => '4'],
            ['view_count' => 'DESC'],
            10
        );

        $healthNews = $this->entityManager->getRepository(News::class)->findBy(
            ['status' => 'published', 'category' => '5'],
            ['view_count' => 'DESC'],
            10
        );



        return $this->render('homepage/index.html.twig', [
            'user' => $user,
            'popularNews' => $popularNews,
            'latestNews' => $latestNews,
            'sportsNews' => $sportsNews,
            'politicsNews' => $politicsNews,
            'financeNews' => $financeNews,
            'technologyNews' => $technologyNews,
            'healthNews' => $healthNews,


        ]);
    }


    #[Route('/dashboard', name: 'app_dashboard')]
    public function editor(Security $security): Response
    {
        $user = $security->getUser();
        return $this->render('editor/dashboard.html.twig', [
            'user' => $user
        ]);

    }

    #[Route('/sidebar', name: 'app_sidebar')]
    public function sidebar(Security $security): Response
    {
        $user = $security->getUser();

        $counts = [];
        $statuses = ['waiting', 'editorreview', 'accepted', 'denied', 'edit_request_response'];

        foreach ($statuses as $status) {
            $count = $this->entityManager->getRepository(News::class)
                ->createQueryBuilder('n')
                ->select('COUNT(n.id)')
                ->where('n.status = :status')
                ->setParameter('status', $status)
                ->getQuery()
                ->getSingleScalarResult();

            $counts[$status] = $count;
        }

        $waitingCount = $counts['waiting'];
        $editorReviewCount = $counts['editorreview'];
        $acceptedCount = $counts['accepted'];
        $deniedCount = $counts['denied'];
        $editorRequestCount = $counts['edit_request_response'];

        $user = $security->getUser();
        return $this->render('sidebar_template.twig', [
            'user' => $user,
            'waitingCount' => $waitingCount,
            'editorReviewCount' => $editorReviewCount,
            'acceptedCount' => $acceptedCount,
            'deniedCount' => $deniedCount,
            'editorRequestCount' => $editorRequestCount,
        ]);

    }


//    #[Route('/profile/{id}/edit', name: 'app_profiledit')]
//    public function profiledit($id, Request $request, Security $security): Response
//    {
//        $User = $this->UserRepository->find($id);
//        if (!$User) {
//            $this->addFlash(
//            'usernotfound',
//            'User Not Found!'
//        );
//           return $this->redirectToRoute('app_homepage');
//        }
//        $email = $User->getUserIdentifier();
//
//
//        $secUser = $security->getUser();
//        $userEmail = $secUser->getUserIdentifier();
//
//        if ($email !== $userEmail) {
//            $this->addFlash(
//                'profileaccesdenied',
//                'Acces Denied!'
//            );
//            return $this->redirectToRoute('app_profile');
//        }
//
//        $form = $this->createForm(ProfileEditType::class, $User);
//
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//            $User->setName($form->get('name')->getData());
//            $User->setSurname($form->get('surname')->getData());
//
//            $this->entityManager->persist($User);
//            $this->entityManager->flush();
//
//            $this->addFlash(
//                'update',
//                'Update Successfully!'
//            );
//
//            return $this->redirectToRoute('app_profile');
//        }
//
//        return $this->render('profile_edit.html.twig', [
//            'user' => $User,
//            'form' => $form->createView()
//        ]);
//    }
}
