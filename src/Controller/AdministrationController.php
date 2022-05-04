<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdministrationController extends AbstractController
{
    #[Route('/administration', name: 'app_admin')]
    public function index(entityManagerInterface $entityManager): Response
    {

        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('administration/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/administration/delete/{id}', name: 'app_user_delete')]
    public function deleteUser(int $id, EntityManagerInterface $entityManager): Response
    {
        //On va chercher l'id du user sur lequel on clique
        $user = $entityManager->getRepository(User::class)->findOneBy(['id'=>$id]);

        //on le remove de la base de donnée
        if (!in_array("ROLE_ADMIN", $user->getRoles())){
            $entityManager->remove($user);
            $entityManager->flush();
        }

        //on reload la page
        return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
    }
}
