<?php

namespace App\Controller;

use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Comment;
use App\Entity\Quack;
use App\Entity\Duck;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment", name="comment")
     */
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    /**
     * @Route("quack/{id}/comment/add", name="add_comment", requirements={"id"="\d+"})
     */
    public function add(Request $request, int $id, UserInterface $user): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        try {
            $data = $request->request->get('comment');
            $quack = $entityManager->find(Quack::class, $id);
            $duck = $entityManager->find(Duck::class, $user->getId());

            $comment = new Comment();

            $comment->setQuack($quack);
            $comment->setDuck($duck);
            $comment->setText($data['text']);

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('feed');
        } catch (Exception $e) {
            return new Response($e);
        }
    }

    /**
     * @Route("/comment/remove/{id}", name="add_comment", requirements={"id"="\d+"})
     */
    public function remove(Request $request, int $id, UserInterface $user): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comment = $entityManager->find(Comment::class, $id);

        if (!$comment) {
            return new Response('404 comment not found');
        }
        
        if ($user->getId() !== $comment->getDuck()->getId() && !$user->isAdmin()) {
            return new response('bah non en fait');
        }
        
        $entityManager->remove($comment);
        $entityManager->flush();
        return $this->redirectToRoute('feed');
    }
}
