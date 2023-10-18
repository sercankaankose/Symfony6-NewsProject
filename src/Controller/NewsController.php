<?php

namespace App\Controller;

use App\Entity\EditRequest;
use App\Entity\News;
use App\Entity\Notification;
use App\Form\NewsType;
use App\Repository\ContentRepository;
use App\Repository\EditRequestRepository;
use App\Repository\NewsRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class NewsController extends AbstractController
{
    private UserRepository $UserRepository;
    private NewsRepository $newsRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $UserRepository, NewsRepository $newsRepository, EntityManagerInterface $entityManager)
    {
        $this->UserRepository = $UserRepository;
        $this->newsRepository = $newsRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/post/{id}', name: 'app_post')]
    public function post($id, Security $security, NewsRepository $newsRepository, EntityManagerInterface $entityManager,
                         ContentRepository $contentRepository, Request $request): Response
    {
        $user = $security->getUser();
        $categories = $contentRepository->findAll();
        $news = $newsRepository->findOneBy(['id' => $id]);

        $popularNews = $this->entityManager->getRepository(News::class)->findBy(
            ['status' => 'published'],
            ['view_count' => 'DESC'],
            6);

        $newsAuthor = $news->getAuthor();
        $newsCategory = $news->getCategory();
        $criteria = [
            'status' => 'published',
            'category' => $newsCategory,
        ];
        $trendingNews = $this->entityManager->getRepository(News::class)->findBy($criteria, ['view_count' => 'DESC']);


        $latestNews = $this->entityManager->getRepository(News::class)->findBy(
            ['status' => 'published'],
            ['published_at' => 'DESC'],
            6);


        if ($news && $news->getStatus() === 'published') {
            $news->setViewCount($news->getViewCount() + 1);

            $entityManager->flush();

            return $this->render('single-post.html.twig', [
                'categories' => $categories,
                'news' => $news,
                'id' => $id,
                'popularNews' => $popularNews,
                'latestNews' => $latestNews,
                'trendingNews' => $trendingNews,

            ]);
        } else {
            $this->addFlash('register', 'This News Is Not Published');
            return $this->redirectToRoute('app_homepage');
        }

    }

    #[Route('/look/{id}', name: 'app_look')]
    public function postlook($id, Security $security, NewsRepository $newsRepository, ContentRepository $contentRepository): Response
    {
        $user = $security->getUser();
        $categories = $contentRepository->findAll();
        $news = $newsRepository->findOneBy(['id' => $id]);


        $newsAuthor = $news->getAuthor();
        $newsCategory = $news->getCategory();


        return $this->render('look-post.html.twig', [
            'categories' => $categories,
            'news' => $news,
            'id' => $id,

        ]);
    }


    #[Route('/news/edit/{id}', name: 'app_news_edit')]
    public function editWaitingNews($id, EntityManagerInterface $entityManager, Security $security, Request $request): Response
    {
        if (!$security->getUser()) {

            $this->addFlash('needlog', 'You must be logged in to edit news.');
            return $this->redirectToRoute('app_login');
        }
        $news = $entityManager->getRepository(News::class)->find($id);

        if (!$news || $news->getStatus() !== 'waiting') {
            $this->addFlash('permıserror', 'You do not have permission to edit this news.');
            return $this->redirectToRoute('app_profile');

        }
        $user = $security->getUser();
        $author = $news->getAuthor();


        if ($user !== $author) {
            throw $this->createAccessDeniedException('You do not have permission to access this news.');
        }

        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newsImage = $form->get('image')->getData();
            if ($newsImage) {
                $newFilename = uniqid() . '.' . $newsImage->guessExtension();

                try {
                    $newsImage->move(
                        $this->getParameter('kernel.project_dir') . '/public/news',
                        $newFilename
                    );
                    $news->setImage('/news/' . $newFilename);
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
            }
            $entityManager->persist($news);
            $entityManager->flush();

            $this->addFlash('successeditnews', 'News updated successfully.');
            return $this->redirectToRoute('app_profile');
        }


        return $this->render('news/edit.html.twig', [
            'form' => $form->createView(),
            'news' => $news
        ]);
    }

//=========================================

//user control sent_To_edit


    #[Route('review/edit/news/{id}', name: 'app_news_send_edit')]
    public function sendEditAction($id, EntityManagerInterface $entityManager, Security $security): Response
    {


        $news = $entityManager->getRepository(News::class)->find($id);

        $user = $security->getUser();
        $author = $news->getAuthor();


        if ($user !== $author) {
            throw $this->createAccessDeniedException('You do not have permission to access this news.');
        }
        if (!$news || $news->getStatus() !== 'sent_to_edit') {
            $this->addFlash('permıserror', 'You do not have permission to edit this news.');
            return $this->redirectToRoute('app_profile');

        }


        $editRequest = $this->entityManager->getRepository(EditRequest::class)->findOneBy(['news' => $id, 'status' => 'waiting']);



        return $this->render('news/reviewsendtoedit.html.twig', [
            'editRequest' => $editRequest,
            'id' => $id,
            'news' => $news,
        ]);

    }

//    ----------------------------------------
    #[Route('/accept/edit/{id}/{editrequestid}', name: 'app_accept_edit_request')]
    public function acceptEditRequest($id, Security $security, $editrequestid): Response
    {
        $news = $this->entityManager->getRepository(News::class)->find($id);


        $user = $security->getUser();
        $author = $news->getAuthor();


        if ($user !== $author) {
            throw $this->createAccessDeniedException('You do not have permission to access this news.');
        }
        if (!$news || $news->getStatus() !== 'sent_to_edit') {
            $this->addFlash('permıserror', 'You do not have permission to edit this news.');
            return $this->redirectToRoute('app_profile');

        }


        $editRequest = $this->entityManager->getRepository(EditRequest::class)->find($editrequestid);

        $editRequest->setStatus('asking_editor');
        $news->setStatus('edit_request_response');

        $now = new \DateTime();
        $editRequest->setAcceptedAt($now);

        $now = new DateTime();
        $notify = new Notification();
        $notify->setStatus('time_for_edit_accept');
        $notify->setAuthor($author);
        $notify->setEditor($user);

        $notify->setDateAt($now);
        $notify->setNotifications(0);


        $this->entityManager->persist($notify);
        $this->entityManager->flush();


        $this->entityManager->persist($editRequest);
        $this->entityManager->flush();
        $this->addFlash('permıserror', 'Time was requested for editing');

        //return $this->redirectToRoute('editing_news', ['id' => $editRequest->getNews()->getId(), 'editrequestid' => $editRequest->getId()]);
        return $this->redirectToRoute('app_profile');
    }


    #[Route('/editing/news/{id}', name: 'editing_news')]
    public function senttoediting($id, Security $security, EditRequestRepository $editRequestRepository, Request $request): Response
    {

        $news = $this->entityManager->getRepository(News::class)->find($id);

        $user = $security->getUser();
        $author = $news->getAuthor();

        if (!$news || $news->getStatus() !== 'in_progress') {
            $this->addFlash('permıserror', 'You do not have permission to edit this news.');
            return $this->redirectToRoute('app_profile');

        }
        if ($user !== $author) {
            throw $this->createAccessDeniedException('You do not have permission to access this news.');
        }

        $editRequest = $editRequestRepository->createQueryBuilder('e')
            ->where('e.news = :news')
            ->setParameter('news', $news)
            ->orderBy('e.request_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $now = new \DateTime();

        if ($editRequest->getUpdatedAt() < $now) {
            $editRequest->setStatus('waiting');
            $news->setStatus('sent_to_edit');
            $this->entityManager->persist($editRequest);
            $this->entityManager->persist($news);
            $this->entityManager->flush();

            $this->addFlash('timeisout', 'Editing time has expired ');
            return $this->redirectToRoute('app_profile');
        }


        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newsImage = $form->get('image')->getData();
            if ($newsImage) {
                $newFilename = uniqid() . '.' . $newsImage->guessExtension();

                try {
                    $newsImage->move(
                        $this->getParameter('kernel.project_dir') . '/public/news',
                        $newFilename
                    );
                    $news->setImage('/news/' . $newFilename);
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
            }
            $this->entityManager->persist($news);
            $this->entityManager->flush();

            $this->addFlash('successeditnews', 'News updated successfully.');
            return $this->redirectToRoute('app_edited_news', ['id' => $editRequest->getNews()->getId(), 'editrequestid' => $editRequest->getId()]);
        }

        return $this->render('news/editing_stedit.html.twig', [
            'id' => $id,
            'news' => $news,
            'editrequest' => $editRequest,
            'form' => $form->createView(),

        ]);

    }


    #[Route('/edited/news/{id}/{editrequestid}', name: 'app_edited_news')]
    public function editedfunction($id, $editrequestid, Security $security): Response
    {
        $news = $this->entityManager->getRepository(News::class)->find($id);
        $editRequest = $this->entityManager->getRepository(EditRequest::class)->find($editrequestid);

        $user = $security->getUser();
        $author = $news->getAuthor();

        if (!$news || $news->getStatus() !== 'in_progress') {
            $this->addFlash('permıserror', 'You do not have permission to edit this news.');
            return $this->redirectToRoute('app_profile');

        }
        if ($user !== $author) {
            throw $this->createAccessDeniedException('You do not have permission to access this news.');
        }

        $news->setStatus('editorreview');
        $editRequest->setStatus('finished');


        $this->entityManager->persist($news);
        $this->entityManager->persist($editRequest);

        $this->entityManager->flush();


        return $this->redirectToRoute('app_profile');


    }

//----------------------------------------------------------------------

    #[Route('/denied/news/{id}', name: 'app_delete_news')]
    public function deletenews($id, Security $security): Response
    {
        $news = $this->entityManager->getRepository(News::class)->find($id);

        $user = $security->getUser();
        if ($news->getAuthor() !== $user) {
            throw new AccessDeniedException('You do not have permission to access this News.');
        }


        if (!$news) {
            $this->addFlash('error', 'News not found.');
            $this->redirectToRoute('app_profile');
        }
        $editRequests = $this->entityManager->getRepository(EditRequest::class)->findBy(['news' => $news]);

        foreach ($editRequests as $editRequest) {
            $this->entityManager->remove($editRequest);
        }
        $news->setStatus('denied');
        $this->entityManager->persist($news);

        $this->entityManager->flush();

        $this->addFlash('deletenew', 'News Denied successfully.');

        return $this->redirectToRoute('app_profile');

    }

    #[Route('/delete/{id}', name: 'app_delete')]
    public function deletefunction($id, Security $security): Response
    {
        $news = $this->entityManager->getRepository(News::class)->findOneBy(['id' => $id]);
        $user = $security->getUser();
        $author = $news->getAuthor();

        if (!$news) {
            throw $this->createAccessDeniedException('You do not have permission to access this news');
        }
        if ($news->getStatus() !== 'denied') {
            $this->addFlash('permıserror', 'You do not have permission to delete this news');
            return $this->redirectToRoute('app_profile');
        }

        if ($user !== $author) {
            throw $this->createAccessDeniedException('You do not have permission to access this news');
        }


        $editRequests = $this->entityManager->getRepository(EditRequest::class)->findBy(['news' => $news]);
        try {
            if ($editRequests) {
                foreach ($editRequests as $editRequest) {
                    $this->entityManager->remove($editRequest);
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();

        }
        $this->entityManager->flush();
        if ($news) {
            $news->setEditor(null);
            $this->entityManager->persist($news);
            $this->entityManager->remove($news);
        }

        $this->entityManager->flush();


        return $this->redirectToRoute('app_profile');
    }


}
