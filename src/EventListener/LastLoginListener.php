<?php

namespace App\EventListener;

use AllowDynamicProperties;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class LastLoginListener
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[AsEventListener(event: 'security.interactive_login')]
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        if ($user instanceof User){
            $user->setLastLoginAt(new \DateTimeImmutable());
            $this->entityManager->flush(); // pas besoin d'appeler persist ici
        }
    }
}
