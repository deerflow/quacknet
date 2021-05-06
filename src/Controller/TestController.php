<?php

namespace App\Controller;

use App\Services\MailService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(MailService $mailService): Response
    {
        try {
            $mailService->sendMailOnComment('alu.florian@gmail.com', 'Mails/new-comment.html.twig', 'Test');
        } catch (Exception $e) {
            return new Response($e);
        }
        return $this->render('base.html.twig');
    }
}
