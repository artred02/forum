<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\AnsweringType;
use App\Form\PostType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(entityManagerInterface $entityManager): Response
    {

        $posts = $entityManager->getRepository(Post::class)->findAll();

        return $this->render('main/index.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/post/create', name:'app_post_create')]
    public function formPost(entityManagerInterface $entityManager, Request $request){

        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()){
            $author = $entityManager->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
            $post->setAuthor($author);
            $post->setCreationDate(new DateTime('now'));

            //On le persist
            $entityManager->persist($post);
            $entityManager->flush();

            //redirection vers les items
            return $this->redirect($this->generateUrl('app_main'));
        }

        //un render vers la view où l'on va l'utiliser
        return $this->render('forms/post.html.twig', [
            'form' => $form->createView()
        ]);

    }

    #[Route('/post/{id}', name: 'app_post')]
    public function post(int $id, entityManagerInterface $entityManager): Response
    {

        $post = $entityManager->getRepository(Post::class)->findOneBy(['id' => $id]);
        $comments = $entityManager->getRepository(Comment::class)->findBy(['post' => $post]);

        return $this->render('main/post.html.twig', [
            'post' => $post,
            'comment' => $comments
        ]);
    }

    #[Route('/post/answer/{id}', name:'app_post_answer')]
    public function answer(int $id, entityManagerInterface $entityManager, Request $request){

        $post = $entityManager->getRepository(Post::class)->findOneBy(['id' => $id]);
        $comment = new Comment();

        $form = $this->createForm(AnsweringType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $author = $entityManager->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
            $comment->setAuthor($author);
            $comment->setCreationDate(new DateTime('now'));
            $comment->setPost($post);

            //On le persist
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('app_main'));
        }

        //un render vers la view où l'on va l'utiliser
        return $this->render('forms/answer.html.twig', [
            'form' => $form->createView(),
            'post' => $post
        ]);

    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(entityManagerInterface $entityManager): Response
    {

        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);

        return $this->render('main/profile.html.twig', [
            'user' => $user
        ]);
    }
}
