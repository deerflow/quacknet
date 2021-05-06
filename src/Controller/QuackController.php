<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Quack;
use App\Entity\Tag;
use App\Form\CommentType;
use App\Form\QuackType;
use App\Repository\QuackRepository;
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

    /**
     * @Route("/", name="feed")
     */
    public function getFeed(Request $request, QuackRepository $quackRepository): Response
    {
        $doctrine = $this->getDoctrine();
        $search = $request->query->get('search');

        // TO DO : Au lieu de faire des array_reverse on pourrait ORDER BY created_at ? Vraie question ?

        $quacks = array_reverse($quackRepository->findBySearchTerm($search));

        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $newQuackForm = $this->createForm(QuackType::class, new Quack(), [
                'action' => $this->generateUrl('create_quack')
            ]);

            foreach ($quacks as $quack) {
                $quack->commentForm = $this->createForm(CommentType::class, new Comment(), [
                    'action' => $this->generateUrl('add_comment', ['id' => $quack->getId()]),
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

    /**
     * @param UserInterface $user
     * @param Request $request
     * @return Response
     * @Route("/quack/create", name="create_quack", methods={"POST"})
     */
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

    /**
     * @param Request $request
     * @param int $id
     * @param UserInterface $user
     * @return Response
     * @Route("/quack/remove/{id}", name="remove_quack", methods={"DELETE"})
     */
    public function removeOne(Request $request, int $id, UserInterface $user): Response
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
