<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\TourCanceladoEvent;
use App\Services\MailerService;



class TourCanceladoSubscriber implements EventSubscriberInterface
{
    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    public static function getSubscribedEvents()
    {
        return [
            TourCanceladoEvent::NAME => 'onTourCancelado',
        ];
    }
    
    public function onTourCancelado(TourCanceladoEvent $event)
    {
      
        // Lógica para manejar el evento
        $tour = $event->getTour();
        // Envía un correo electrónico a todos los que tienen reservado el tour
        $this->mailerService->mandaemail("Tour cancelada");
            
        }

        
}
