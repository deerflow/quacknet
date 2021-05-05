<?php

namespace App\Controller;

use App\Entity\Quack;
use App\Entity\Duck;
use App\Entity\Tag;
use App\Entity\Comment;
use App\Form\QuackType;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function getFeed(Request $request): Response
    {
        $doctrine = $this->getDoctrine();
        $securityContext = $this->container->get('security.authorization_checker');
        $search = $request->query->get('search');

        if (!$search) {
            $quacks = array_reverse($doctrine->getRepository(Quack::class)->findAll());
        } else {
            $quacks = array_reverse($doctrine->getRepository(Quack::class)->findBySearchTerm($search));
        }

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $newQuackForm = $this->createForm(QuackType::class, new Quack(), [
                'action' => '/create',
                'method' => 'POST'
            ]);

            foreach ($quacks as $oneQuack) {
                $oneQuack->commentForm = $this->createForm(CommentType::class, new Comment(), [
                    'action' => '/quack/' . $oneQuack->getId() . '/comment/add',
                    'method' => 'POST'
                ])->createView();
            }

            return $this->render('quack/feed.html.twig', [
                'quacks' => $quacks,
                'form' => $newQuackForm->createView(),
            ]);
        }

        return $this->render('quack/feed.html.twig', [
            'quacks' => $quacks
        ]);
    }

    public function createOne(UserInterface $user, Request $request): Response
    {
        $quack = new Quack();

        $data = $request->request->get('quack');
        $quack->setContent($data['content']);
        $quack->setPhoto($data['photo']);
        $quack->setCreatedAt(new \DateTime());
        $quack->setAuthor($user);

        $entityManager = $this->getDoctrine()->getManager();

        $tags = $this->formatHashtags($data['hashtags']);
        foreach ($tags as $tagText) {
            $tag = new Tag();
            $tag->setText($tagText);

            $quack->addHashtag($tag);
            $entityManager->persist($tag);
        }

        $entityManager->persist($quack);
        $entityManager->flush();
        return $this->redirectToRoute('feed');
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

        $tags = $this->formatHashtags($request->query->get('tags'));
        if ($tags) {
            foreach ($quack->getHashtags() as $hashtag) {
                $quack->removeHashtag($hashtag);
            }
            foreach ($tags as $tagText) {
                $tag = new Tag();
                $tag->setText($tagText);
                $quack->addHashtag($tag);
            }
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new Response('Updated quack with the id : ' . $id);
    }

    public function remove(Request $request, int $id, UserInterface $user): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $quack = $entityManager->find(Quack::class, $id);

        if (!$quack) {
            return new Response('404 quack not found');
        }

        if ($user->getId() !== $quack->getAuthor()->getId() && !$user->isAdmin()) {
            return new response('bah non en fait');
        }

        $entityManager->remove($quack);
        $entityManager->flush();
        return $this->redirectToRoute('feed');
    }

    public function formatHashtags(string $text)
    {
        $trimmedArray = explode(' ', trim(str_replace("/\s+/", ' ', $text)));
        return array_filter(array_map(function ($element) {
            if (strpos($element, '#') === 0) $element = substr($element, 1);
            return $element;
        }, $trimmedArray));
    }
}
