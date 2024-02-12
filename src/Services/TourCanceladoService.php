<?php
namespace App\Service;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Event\TourCanceladoEvent;

class TourCanceladoService
{
    private $dispatcher;
    
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    public function tourCancelado($data)
    {
        
        // Despachar el evento
        $event = new TourCanceladoEvent($data);
        $this->dispatcher->dispatch($event, TourCanceladoEvent::NAME);
        
        return $data;
    }
}
