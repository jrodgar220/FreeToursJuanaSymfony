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
      
        $tour = $event->getTour();
        $cuerpomensaje="El tour " . $tour->getRuta()->getTitulo(). "con fecha ".$tour->getFecha()->format('Y-m-d')      . 
        " a las " . $tour->getHora()->format('H:i:s'). " el cual reservó, ha sido cancelado";
        foreach ($tour->getReservas() as $reserva) {
            $mail= $reserva->getUsuario()->getEmail();
            $this->mailerService->mandaemail($cuerpomensaje, "Tour cancelado",$mail);
        }
            
        }

        
}
