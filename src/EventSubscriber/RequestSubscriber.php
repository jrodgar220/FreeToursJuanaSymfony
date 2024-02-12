<?php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class RequestSubscriber implements EventSubscriberInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            //KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        // Accede al objeto Request para obtener información sobre la solicitud
        $request = $event->getRequest();
        $method = $request->getMethod();
        $uri = $request->getUri();

        // Envía un correo electrónico cada vez que se recibe una solicitud
        $this->sendEmail("Solicitud recibida: $method $uri");
    }

    private function sendEmail($message)
    {
        // Implementa la lógica para enviar el correo electrónico utilizando el servicio Mailer
        $email = (new Email())
            ->from('noreply@example.com')
            ->to('admin@example.com')
            ->subject('Nueva Solicitud')
            ->text($message);

        $this->mailer->send($email);
    }
}

