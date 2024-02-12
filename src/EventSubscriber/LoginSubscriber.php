<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class LoginSubscriber implements EventSubscriberInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [

            SecurityEvents::INTERACTIVE_LOGIN=>'onInteractiveLogin',
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event){
        $user = $event->getAuthenticationToken()->getUser();

        // Envía un correo electrónico cada vez que se recibe una solicitud
        $this->sendEmail("Login hecho de ". $user->getNombre());
    
    }

    private function sendEmail($message)
    {
        // Implementa la lógica para enviar el correo electrónico utilizando el servicio Mailer
        $email = (new Email())
            ->from('noreply@example.com')
            ->to('admin@example.com')
            ->subject('Nuevo login')
            ->text($message);

        $this->mailer->send($email);
    }
}