<?php

// src/Controller/ProductController.php
namespace App\Services;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class MailerService
{
    public function __construct(
        private MailerInterface $mailer,
    ) {    }


    public function mandaemail($cuerpo, $asunto, $destinatario, ?string $pdfContent = null, ?string $pdfFileName = null): bool
    {
        $email = (new Email())
            ->from('jrodgar220@g.educaand.es')
            ->to($destinatario)
            ->subject($asunto)
            ->text($cuerpo)
            ->html('<p>See Twig integration for better HTML integration!</p>');

        // Si se proporciona contenido de PDF y nombre de archivo PDF, adjuntar el archivo PDF al correo electrónico
        if ($pdfContent !== null && $pdfFileName !== null) {
            $email->attach($pdfContent, $pdfFileName, 'application/pdf');
        }

        $this->mailer->send($email);

        return true;
    }
}
