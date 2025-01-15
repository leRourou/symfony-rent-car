<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailService
{
    private $mailer;
    private $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendEmail(string $to, string $subject, string $template, array $context = []): void
    {
        $content = $this->twig->render($template, $context);

        $email = (new Email())
            ->from('proutttttttttt@gmail.com') 
            ->to($to)
            ->subject($subject)
            ->html($content);

        $this->mailer->send($email);
    }
}
