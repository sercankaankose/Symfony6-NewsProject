<?php

namespace App\Controller;

use App\Entity\EditRequest;
use App\Entity\News;
use App\Entity\Notification;
use App\Params\RoleParams;
use App\Repository\EditRequestRepository;
use App\Repository\NewsRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class EditorController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


//---------------------------------------------------
    #[Route("/editor/review", name: "editor_review")]
    public function review(NewsRepository $newsRepository, AuthorizationCheckerInterface $authorizationChecker, Security $security): Response

    {


        $user = $security->getUser();
        if (!$user){
            return $this->redirectToRoute('app_login');
        }
        $newsList = $newsRepository->findBy(
            ['status' => 'waiting', 'editor' => null],
        );

        $editor = $user->getId();


        if (!in_array('Editor', $user->getRoles())) {
            throw $this->createAccessDeniedException('You do not have permission to access this page.');
        }


        $waitingCount = $this->entityManager->getRepository(News::class)->count(['status' => 'waiting']);
        $editorReviewCount = $this->entityManager->getRepository(News::class)
            ->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.status = :status')
            ->andWhere('n.editor = :editor')
            ->setParameter('status', 'editorreview')
            ->setParameter('editor', $editor)
            ->getQuery()
            ->getSingleScalarResult();
        $acceptedCount = $this->entityManager->getRepository(News::class)->count(['status' => 'accepted']);
        $deniedCount = $this->entityManager->getRepository(News::class)->count(['status' => 'denied']);
        $editorRequestCount = $this->entityManager->getRepository(News::class)->count(['status' => 'edit_request_response', 'editor' => $this->getUser()]);

        return $this->render('editor/review.html.twig', [
            'newsList' => $newsList,

            'waitingCount' => $waitingCount,
            'editorReviewCount' => $editorReviewCount,
            'acceptedCount' => $acceptedCount,
            'deniedCount' => $deniedCount,
            'editorRequestCount' => $editorRequestCount,

            'user' => $user,

        ]);
    }


    #[Route("/editor/revieww", name: "editor_updatereview")]
    public function updatereview(Security $security): Response
    {

        $user = $security->getUser();
        if (!$user){
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('Editor', $user->getRoles())) {
            throw $this->createAccessDeniedException('You do not have permission to access this page.');
        }
        $queryBuilder = $this->entityManager
            ->createQueryBuilder('n')
            ->select('n')
            ->from(News::class, 'n')
            ->where('n.status = :status')
            ->andWhere('n.editor != :editor')
            ->setParameter('status', 'editorreview')
            ->setParameter('editor', $user);

        $newsList2 = $queryBuilder->getQuery()->getResult();

        $editor = $user->getId();
        $waitingCount = $this->entityManager->getRepository(News::class)->count(['status' => 'waiting']);
        $editorReviewCount = $this->entityManager->getRepository(News::class)
            ->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.status = :status')
            ->andWhere('n.editor = :editor')
            ->setParameter('status', 'editorreview')
            ->setParameter('editor', $editor)
            ->getQuery()
            ->getSingleScalarResult();
        $acceptedCount = $this->entityManager->getRepository(News::class)->count(['status' => 'accepted']);
        $deniedCount = $this->entityManager->getRepository(News::class)->count(['status' => 'denied']);
        $editorRequestCount = $this->entityManager->getRepository(News::class)->count(['status' => 'edit_request_response', 'editor' => $this->getUser()]);


        return $this->render('editor/reviewupdate.html.twig', [
            'newsList2' => $newsList2,
            'waitingCount' => $waitingCount,
            'editorReviewCount' => $editorReviewCount,
            'acceptedCount' => $acceptedCount,
            'deniedCount' => $deniedCount,
            'user' => $user,
            'editorRequestCount' => $editorRequestCount,

        ]);
    }

//----------------------------------------------------------------------------
//function assigment
    #[Route("/editor/take/{id}", name: "editor_take")]
    public function takeActionfunction($id, NewsRepository $newsRepository, EntityManagerInterface $entityManager, Security $security): Response
    {


        $news = $newsRepository->find($id);
        $user = $security->getUser();
        if (!$user){
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('Editor', $user->getRoles())) {
            throw $this->createAccessDeniedException('You do not have permission to access this page.');
        }
        if ($news->getStatus() !== 'waiting') {
            throw $this->createAccessDeniedException('The news status is not "waiting".');
        } elseif ($news->getAuthor() === $user) {
            $this->addFlash('takebad', 'Editor and author cannot be the same');
            $this->redirectToRoute('editor_review');
        } else {
            $news->setEditor($this->getUser());
            $news->setStatus('editorreview');

            $entityManager->persist($news);



            $this->entityManager->flush();

            $this->addFlash('takesucces', 'News has been taken.');

        }


        return $this->redirectToRoute('editor_review', [
            'user' => $user,


        ]);
    }

//-------------------------------------------------------
    #[Route("/editor/Check/News", name: "app_check")]
    public function checkNews(NewsRepository $newsRepository): Response
    {
        $user = $this->getUser();

        if (!$user){
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('Editor', $user->getRoles())) {
            throw $this->createAccessDeniedException('You do not have permission to access this page.');
        }
        $editor = $user->getId();

        $newsList = $newsRepository->findBy(['editor' => $editor, 'status' => 'editorreview']);
        $waitingCount = $this->entityManager->getRepository(News::class)->count(['status' => 'waiting']);
        $editorReviewCount = $this->entityManager->getRepository(News::class)
            ->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.status = :status')
            ->andWhere('n.editor = :editor')
            ->setParameter('status', 'editorreview')
            ->setParameter('editor', $editor)
            ->getQuery()
            ->getSingleScalarResult();
        $acceptedCount = $this->entityManager->getRepository(News::class)->count(['status' => 'accepted']);
        $deniedCount = $this->entityManager->getRepository(News::class)->count(['status' => 'denied']);
        $editorRequestCount = $this->entityManager->getRepository(News::class)->count(['status' => 'edit_request_response', 'editor' => $this->getUser()]);


        return $this->render('editor/check.html.twig', [
            'newsList' => $newsList,
            'user' => $user,

            'waitingCount' => $waitingCount,
            'editorReviewCount' => $editorReviewCount,
            'acceptedCount' => $acceptedCount,
            'deniedCount' => $deniedCount,
            'editorRequestCount' => $editorRequestCount,

        ]);
    }

    #[Route("/editor/check/{id}", name: "editor_review_detail")]
    public function reviewdetail($id, NewsRepository $newsRepository, Security $security): Response
    {
        $user = $security->getUser(); if (!$user){
        return $this->redirectToRoute('app_login');
    }
        if (!in_array('Editor', $user->getRoles())) {
            throw $this->createAccessDeniedException('You do not have permission to access this page.');
        }

        $news = $newsRepository->find($id);
        $editor = $user->getId();
        
        if ($news->getEditor() !== $user) {
            throw $this->createAccessDeniedException('You are not the editor of this news.');
        }
        if ($news->getStatus() !== 'editorreview') {
            return $this->redirectToRoute('app_check');

        }

        $waitingCount = $this->entityManager->getRepository(News::class)->count(['status' => 'waiting']);
        $editorReviewCount = $this->entityManager->getRepository(News::class)
            ->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.status = :status')
            ->andWhere('n.editor = :editor')
            ->setParameter('status', 'editorreview')
            ->setParameter('editor', $editor)
            ->getQuery()
            ->getSingleScalarResult();
        $acceptedCount = $this->entityManager->getRepository(News::class)->count(['status' => 'accepted']);
        $deniedCount = $this->entityManager->getRepository(News::class)->count(['status' => 'denied']);
        $editorRequestCount = $this->entityManager->getRepository(News::class)->count(['status' => 'edit_request_response', 'editor' => $this->getUser()]);


        return $this->render('editor/check-news.html.twig', [
            'newsId' => $id,
            'news' => $news,
            'user' => $user,

            'waitingCount' => $waitingCount,
            'editorReviewCount' => $editorReviewCount,
            'acceptedCount' => $acceptedCount,
            'deniedCount' => $deniedCount,
            'editorRequestCount' => $editorRequestCount,

        ]);
    }


    #[Route("/news/change-status/{newsId}/{status}", name: "change_news_status")]
    public function changeNewsStatusfunction($newsId, $status, Request $request): Response
    {
        $user = $this->getUser();  if (!$user){
        return $this->redirectToRoute('app_login');
    }
        if (!in_array('Editor', $user->getRoles())) {
            throw $this->createAccessDeniedException('You do not have permission to access this page.');
        }

        $news = $this->entityManager->getRepository(News::class)->find($newsId);

        if ($news->getEditor() !== $user) {
            throw $this->createAccessDeniedException('You are not the editor of this news.');
        }

        if (!$news) {
            $this->redirectToRoute('app_check');
        }
        $news->setStatus('in_progress');
        $this->entityManager->persist($news);

        if ($status == 'sent_to_edit') {
            $editorNote = $request->get('editor_note');

            $editRequest = new EditRequest();
            $editor = $this->getUser();


            $editRequest->setEditorNote($editorNote);
            $editRequest->setRequestAt(new \DateTime());

            $now = new  DateTime();
            $updatedAt = clone $now;
            $updatedAt->add(new \DateInterval('P1D'));
            $editRequest->setUpdatedAt($updatedAt);

            $editRequest->setStatus('in_progress');
            $editRequest->setNews($news);
            $editRequest->setEditor($editor);


            $this->entityManager->persist($editRequest);

        $now = new DateTime();
        $notify = new Notification();
        $notify->setIsRead(false);
        $notify->setContent('Sent for news editing');
        $notify->setAddedAt($now);
        $notify->setPerson($news->getAuthor());
        $notify->setNews($news);
        $notify->setDestination('/editing/news/');

        $this->entityManager->persist($notify);

        }
$this->entityManager->flush();

        $flashMessage = '';
        if ($status == 'accepted') {
            $flashMessage = 'News accepted!';

        } elseif ($status == 'denied') {
            $flashMessage = 'News denied.';

        } elseif ($status == 'sent_to_edit') {
            $flashMessage = 'News sent for editing.';
        }

        $this->addFlash('info', $flashMessage);


        return $this->redirectToRoute('app_check', [
            'newsId' => $newsId,
        ]);
    }

//---------------------------------------------------------------------
//published
    #[Route("/news/published", name: "app_published")]
    public function published(NewsRepository $newsRepository, Security $security): Response
    {
        $user = $security->getUser();if (!$user){
        return $this->redirectToRoute('app_login');
    }
        if (!in_array('Editor', $user->getRoles())) {
            throw $this->createAccessDeniedException('You do not have permission to access this page.');
        }

        $newsList = $newsRepository->findBy(
            ['status' => 'accepted'],
        );


        $editor = $user->getId();


        $waitingCount = $this->entityManager->getRepository(News::class)->count(['status' => 'waiting']);
        $editorReviewCount = $this->entityManager->getRepository(News::class)
            ->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.status = :status')
            ->andWhere('n.editor = :editor')
            ->setParameter('status', 'editorreview')
            ->setParameter('editor', $editor)
            ->getQuery()
            ->getSingleScalarResult();
        $acceptedCount = $this->entityManager->getRepository(News::class)->count(['status' => 'accepted']);
        $deniedCount = $this->entityManager->getRepository(News::class)->count(['status' => 'denied']);
        $editorRequestCount = $this->entityManager->getRepository(News::class)->count(['status' => 'edit_request_response', 'editor' => $this->getUser()]);


        return $this->render('editor/published.html.twig', [
            'newsList' => $newsList,
            'user' => $user,

            'waitingCount' => $waitingCount,
            'editorReviewCount' => $editorReviewCount,
            'acceptedCount' => $acceptedCount,
            'deniedCount' => $deniedCount,
            'editorRequestCount' => $editorRequestCount,

        ]);
    }

    #[Route("/news/published/{id}", name: "publish")]
    public function publish($id, NewsRepository $newsRepository, EntityManagerInterface $entityManager, Security $security): Response
    {
        $news = $newsRepository->find($id);
        $user = $security->getUser();
        if (!$user){
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('Editor', $user->getRoles())) {
            throw $this->createAccessDeniedException('You do not have permission to access this page.');
        }

        $now = new DateTime();
        $news->setStatus('published');
        $news->setPublishedAt($now);
        $news->setViewCount(0);

        $now = new DateTime();
        $notify = new Notification();
        $notify->setIsRead(false);
        $notify->setContent('News Published');
        $notify->setAddedAt($now);
        $notify->setPerson($news->getAuthor());
        $notify->setNews($news);
        $notify->setDestination('/post/');

        $this->entityManager->persist($notify);
        $entityManager->persist($news);

        $entityManager->flush();

        $this->addFlash('publish', 'News has been made Public.');


        return $this->redirectToRoute('app_published', [
            'user' => $user,

        ]);
    }
    //----------------------------------------------------------------------------------
    //denied
    #[Route("/news/denied", name: "app_denied")]
    public function denied(NewsRepository $newsRepository, Security $security): Response
    {
        $user = $this->getUser();
        if (!$user){
            return $this->redirectToRoute('app_login');
        }

        if (!in_array('Editor', $user->getRoles())) {
            throw $this->createAccessDeniedException('You do not have permission to access this page.');
        }


        $editor = $user->getId();

        $newsList = $newsRepository->findBy(['status' => 'denied']);


        $waitingCount = $this->entityManager->getRepository(News::class)->count(['status' => 'waiting']);
        $editorReviewCount = $this->entityManager->getRepository(News::class)
            ->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.status = :status')
            ->andWhere('n.editor = :editor')
            ->setParameter('status', 'editorreview')
            ->setParameter('editor', $editor)
            ->getQuery()
            ->getSingleScalarResult();
        $acceptedCount = $this->entityManager->getRepository(News::class)->count(['status' => 'accepted']);
        $deniedCount = $this->entityManager->getRepository(News::class)->count(['status' => 'denied']);
        $editorRequestCount = $this->entityManager->getRepository(News::class)->count(['status' => 'edit_request_response', 'editor' => $this->getUser()]);


        return $this->render('editor/denied.html.twig', [
            'newsList' => $newsList,
            'user' => $user,

            'waitingCount' => $waitingCount,
            'editorReviewCount' => $editorReviewCount,
            'acceptedCount' => $acceptedCount,
            'deniedCount' => $deniedCount,
            'editorRequestCount' => $editorRequestCount,

        ]);
    }

    #[Route("/news/denied/{id}", name: "denied")]
    public function deniedfunc($id, NewsRepository $newsRepository, EntityManagerInterface $entityManager, Security $security): Response
    {
        $news = $newsRepository->find($id);
        $user = $security->getUser();

        if (!$user){
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('Editor', $user->getRoles())) {
            throw $this->createAccessDeniedException('You do not have permission to access this page.');
        }
        if ($news->getEditor() !== $user) {
            throw $this->createAccessDeniedException('You are not the editor of this news.');
        }
        $news->setStatus('editorreview');

        $this->entityManager->persist($news);

        $now = new DateTime();

        $this->entityManager->flush();

        $this->addFlash('publish', 'News has been review.');


        return $this->redirectToRoute('app_check', [
            'user' => $user,
        ]);
    }
//------------------------------------------------------------------
//edit request

    #[Route("/news/edit/request", name: "app_editrequest")]
    public function editrequest(NewsRepository $newsRepository, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user){
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('Editor', $user->getRoles())) {
            throw $this->createAccessDeniedException('You do not have permission to access this page.');
        }
        $newsList = $newsRepository->findBy(
            ['status' => 'edit_request_response', 'editor' => $user->getId()],
        );

        $newsWithEditRequests = [];
        foreach ($newsList as $news) {

            $editorId = $user->getId();

            $editRequests = $this->entityManager->getRepository(EditRequest::class)->findOneBy([
                'status' => 'asking_editor',
                'news' => $news,
                'editor' => $editorId
            ]);


            $newsWithEditRequests[] = ['news' => $news, 'edit_request' => $editRequests];
        }

        $editor = $user->getId();

        $waitingCount = $this->entityManager->getRepository(News::class)->count(['status' => 'waiting']);
        $editorReviewCount = $this->entityManager->getRepository(News::class)
            ->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.status = :status')
            ->andWhere('n.editor = :editor')
            ->setParameter('status', 'editorreview')
            ->setParameter('editor', $editor)
            ->getQuery()
            ->getSingleScalarResult();

        $editorReviewCount = $this->entityManager->getRepository(News::class)->count(['status' => 'editorreview', 'editor' => $editor]);
        $acceptedCount = $this->entityManager->getRepository(News::class)->count(['status' => 'accepted']);
        $deniedCount = $this->entityManager->getRepository(News::class)->count(['status' => 'denied']);
        $editorRequestCount = $this->entityManager->getRepository(News::class)->count(['status' => 'edit_request_response', 'editor' => $this->getUser()]);
        return $this->render('editor/editor_edit_request.html.twig', [
            'newsList' => $newsList,
            'user' => $user,
            'newsWithEditRequests' => $newsWithEditRequests,


            'waitingCount' => $waitingCount,
            'editorReviewCount' => $editorReviewCount,
            'acceptedCount' => $acceptedCount,
            'deniedCount' => $deniedCount,
            'editorRequestCount' => $editorRequestCount

        ]);
    }


    #[Route("/edit/request/{id}/{editrequestid}", name: "app_editrequest_redirect")]
    public function editrequestfeedbackfunction($id, $editrequestid, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user){
            return $this->redirectToRoute('app_login');
        }
        $news = $this->entityManager->getRepository(News::class)->find($id);
        $editRequest = $this->entityManager->getRepository(EditRequest::class)->find($editrequestid);

        if (!in_array('Editor', $user->getRoles())) {
            throw $this->createAccessDeniedException('You do not have permission to access this page.');
        }
        if ($news->getEditor() !== $user) {
            throw $this->createAccessDeniedException('You are not the editor of this news.');
        }


        $now = new \DateTime();
        $editRequest->setStatus('in_progress');
        $editRequest->setAcceptedAt($now);

        $news->setStatus('in_progress');
        $updatedAt = clone $now;
        $updatedAt->add(new \DateInterval('P1D'));
        $editRequest->setUpdatedAt($updatedAt);

        $entityManager->persist($editRequest);

        $now = new DateTime();
        $notify = new Notification();
        $notify->setIsRead(false);
        $notify->setContent('Time was given to edit the news');
        $notify->setAddedAt($now);
        $notify->setPerson($news->getAuthor());
        $notify->setNews($news);
        $notify->setDestination('/editing/news/');

        $this->entityManager->persist($notify);
        $entityManager->flush();
        $this->addFlash('timeiscoming', '1 Day Editing Time for the News');

        return $this->redirectToRoute('app_editrequest');

    }

    #[Route('/deletee/{id}', name: 'app_deletee')]
    public function deletefunction($id, Security $security): Response
    {
        $news = $this->entityManager->getRepository(News::class)->findOneBy(['id' => $id]);
        $user = $security->getUser();
        $author = $news->getAuthor();
        if (!$user){
            return $this->redirectToRoute('app_login');
        }
        if (!$news) {
            throw $this->createAccessDeniedException('You do not have permission to access this news');
        }
        if ($news->getStatus() !== 'denied') {
            $this->addFlash('permıserror', 'You do not have permission to delete this news');
            return $this->redirectToRoute('app_denied');
        }



        $notifys = $this->entityManager->getRepository(Notification::class)->findBy(['news' => $news]);

            if ($notifys) {
                foreach ($notifys as $notify) {
                    $this->entityManager->remove($notify);
                }
            }


        $editRequests = $this->entityManager->getRepository(EditRequest::class)->findBy(['news' => $news]);

            if ($editRequests) {
                foreach ($editRequests as $editRequest) {
                    $this->entityManager->remove($editRequest);
                }
            }

        $this->entityManager->flush();
        if ($news) {
            $news->setEditor(null);
            $this->entityManager->persist($news);
            $this->entityManager->remove($news);
        }
        $this->entityManager->flush();


        $this->addFlash('type','News Deleted');
        return $this->redirectToRoute('app_denied');
    }


}
