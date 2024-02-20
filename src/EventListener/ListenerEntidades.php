<?php
namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Services\MailerService;
use App\Entity\Item;

class ListenerEntidades
{

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        // Tu lógica aquí
        echo 'La entidad está a punto de ser persistida.';
    }

    public function postPersist( LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Item) 
            $this->mailerService->mandaemail("Nuevo item creado", "Nuevo item creado", "jmrg00021@gmail.com");

    }
}
