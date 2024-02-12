<?php
namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Entity\Item;
use App\Services\MailerService;


class ItemSubscriber implements EventSubscriber
{
    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        dump('postPersist event triggered for ItemSubscriber');

        $entity = $args->getObject();

        if ($entity instanceof Item) {
            
            $this->mailerService->mandaemail("Nuevo item creado");
        }
    }

    
}
