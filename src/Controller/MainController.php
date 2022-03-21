<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
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
}
