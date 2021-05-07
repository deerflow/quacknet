<?php

namespace App\Services;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailService
{

    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @param MailerInterface $mailer
     * @param Environment $twig
     */
    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param string $subject
     * @param string $from
     * @param string $to
     * @param string $template
     * @param array $parameter
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendMailOnComment(string $to, string $template, string $message): void
    {
        // On envoie le mail
        $email = (new Email())
            // expediteur
            ->from('noreply@mail-quack.com')
            // destinataire
            ->to($to)
            ->subject("Quelqu'un a ecrit un superbe commentaire sur l'un de vos quacks")
            ->html($this->twig->render(
                $template, ['message' => $message])
            );
        // on envoie
        $this->mailer->send($email);
    }
}