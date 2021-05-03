<?php

namespace App\Controller;

use App\Entity\Quack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuackController extends AbstractController
{
    /**
     * @Route("/quack", name="quack")
     */
    public function index(): Response
    {
        return $this->render('quack/index.html.twig', [
            'controller_name' => 'QuackController',
        ]);
    }

    public function feed(): Response
    {
        $quacks = $this->getDoctrine()->getRepository(Quack::class)->findAll();
        return $this->render('quack/feed.html.twig',[ 'quacks' => $quacks]);
    }

    public function createOne(Request $request): Response
    {
        $content = $request->query->get('content');
        $photo = $request->query->get('photo');
        $tags = $request->query->get('tags');

        $quack = new Quack();
        $quack->setContent($content);
        $quack->setPhoto($photo);
        $quack->setTags(explode(',', $tags));

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($quack);
        $entityManager->flush();

        return new Response('Saved quack with id : ' . $quack->getId());
    }

    public function updateOne(Request $request): Response
    {
        $id = $request->query->get('id');
        $quack = $this->getDoctrine()->getRepository(Quack::class)->find($id);

        if (!quack) {
            return new Response('404 quack not found');
        }

        $content = $request->query->get('content');
        if ($content) {
            $quack->setContent($content);
        }
        $author = $request->query->get('author');
        if ($author) {
            $quack->setAuthor($author);
        }
        $photo = $request->query->get('photo');
        if ($photo) {
            $quack->setPhoto($photo);
        }
        $created_at = $request->query->get('created_at');
        if ($created_at) {
            $quack->setDate($created_at);
        }
        $tags = $request->query->get('tags');
        if ($tags) {
            $quack->setTags($tags);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new Response('Updated quack with the id : ' . $id);
    }

    public function deleteOne(Request $request): Response
    {
        $id = $request->query->get('id');
        $quack = $this->getDoctrine()->getRepository(Quack::class)->find($id);

        if (!quack) {
            return new Response('404 quack not found');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($quack);
        $entityManager->flush();

        return new Response('Deleted quack with the id : ' . $id);
    }
}
