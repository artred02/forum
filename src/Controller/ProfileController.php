<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(entityManagerInterface $entityManager): Response
    {

        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $posts = $entityManager->getRepository(Post::class)->findBy(['author' => $user]);

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }
}
