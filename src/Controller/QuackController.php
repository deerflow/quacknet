<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Quack;
use App\Entity\Tag;
use App\Form\CommentType;
use App\Form\QuackType;
use App\Repository\QuackRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuackController extends AbstractController
{
    /**
     * @Route("/", name="feed")
     */
    public function feed(Request $request, QuackRepository $quackRepository): Response
    {
        $this->getUser();

        $doctrine = $this->getDoctrine();

        $search = $request->query->get('search');
        $quacks = $quackRepository->findBySearchTerm($search);

        if ($this->isGranted('CREATE_QUACK')) {
            $newQuackForm = $this->createForm(QuackType::class, new Quack(), [
                'action' => $this->generateUrl('create'),
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
     * @Route("quack/create", name="create", methods={"POST"})
     * @IsGranted("CREATE_QUACK")
     */
    public function create(Request $request): Response
    {
        $user = $this->getUser();

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

    /**
     * @Route("quack/edit/{id}", name="edit", methods={"GET", "PUT"})
     * @IsGranted("EDIT_QUACK")
     */
    public function edit(Request $request, QuackRepository $quackRepository, int $id): Response
    {
        $content = $request->query->get('content');
        $photo = $request->query->get('photo');
        $tags = $this->formatHashtags($request->query->get('tags'));

        $response = $quackRepository->updateOne($id, $content, $photo, $tags);

        if (!$response) return new Response('404 quack not found');

        return $this->redirectToRoute('feed');
    }

    /**
     * @Route("/quack/remove/{id}", name="remove", methods={"DELETE"})
     */
    public function remove(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $quack = $entityManager->find(Quack::class, $id);

        if (!$quack) {
            return new Response('404 quack not found');
        }

        $this->denyAccessUnlessGranted('DELETE_QUACK', $quack);

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
