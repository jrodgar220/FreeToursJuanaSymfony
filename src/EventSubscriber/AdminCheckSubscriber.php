<?php
namespace App\EventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AdminCheckSubscriber implements EventSubscriberInterface
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $token = $this->tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;
        $isAdmin=false;
        $isGuia=false;
        $isUser=false;
        if ($user!=null && in_array('ROLE_ADMIN', $user->getRoles())) {
            $isAdmin=true;
        }
        if ($user!=null && in_array('ROLE_GUIA', $user->getRoles())) {
            $isGuia=true;
        }
        if ($user!=null && in_array('ROLE_USER', $user->getRoles())) {
            $isUser=true;
        }
        $request = $event->getRequest();
        $request->attributes->set('_is_admin', $isAdmin);
        $request->attributes->set('_is_guia', $isGuia);
        $request->attributes->set('_is_user', $isUser);
    }
}
