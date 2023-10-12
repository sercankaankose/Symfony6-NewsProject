<?php

namespace App\Controller;

use App\Form\ChangePasswordProfileFormType;
use App\Form\ProfileEditType;
use App\Form\UserFormType;
use App\Repository\EditRequestRepository;
use App\Repository\NewsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProfileController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $UserRepository, EntityManagerInterface $entityManager)
    {
        $this->UserRepository = $UserRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(NewsRepository $newsRepository, EditRequestRepository $editRequestRepository ,Request $request): Response
    {
        $user = $this->getUser();
        $statusFilter = $request->query->get('status');
        $categoryFilter = $request->query->get('category');

        $userNews = $newsRepository->findBy(['author' => $user]);

        if ($statusFilter) {
            $userNews = array_filter($userNews, function ($news) use ($statusFilter) {
                return $news->getStatus() === $statusFilter;
            });
        }

        if ($categoryFilter) {
            $userNews = array_filter($userNews, function ($news) use ($categoryFilter) {
                return $news->getCategory()->getId() == $categoryFilter;
            });
        }

        $newsWithCategories = $newsRepository->findAllNewsWithCategories();

        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'userNews' => $userNews,
            'newsWithCategories' => $newsWithCategories,
        ]);
    }



    #[Route('/profile/edit', name: 'app_profil_edit')]
    public function profileEdit(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        $form = $this->createForm(ProfileEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profileImage = $form->get('profileImage')->getData();
            if ($profileImage) {
                $newFilename = uniqid() . '.' . $profileImage->guessExtension();

                try {
                    $profileImage->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFilename
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $user->setProfileImage('/uploads/' . $newFilename);
            }

            $this->entityManager->flush();

            $this->addFlash('profilesucces', 'Profile updated successfully');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/profile_edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }


    #[Route('/profile/change-password', name: 'app_change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordProfileFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if the current password is correct
            $isPasswordValid = $passwordHasher->isPasswordValid($user, $form->get('currentPassword')->getData());

            if (!$isPasswordValid) {
                $this->addFlash('errorr', 'Current password is incorrect.');
            } else {
                // Encode(hash) the new password and set it
                $encodedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                );

                $user->setPassword($encodedPassword);
                $this->entityManager->flush();

                $this->addFlash('passwordsuccess', 'Your password has been changed successfully.');

                return $this->redirectToRoute('app_profile'); // You can change 'app_homepage' to your desired route
            }
        }

        return $this->render('profile/profile_change_password.html.twig', [
            'changePasswordForm' => $form->createView(),
        ]);
    }
}



