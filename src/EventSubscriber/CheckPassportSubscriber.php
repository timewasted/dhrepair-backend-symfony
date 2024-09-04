<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Exception\Authorization\NotConfirmedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

class CheckPassportSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -128],
        ];
    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $user = $event->getPassport()->getUser();
        if (!$user instanceof User) {
            throw new UnsupportedUserException();
        }

        $currentTime = new \DateTimeImmutable();
        if (null !== $user->getConfirmationToken()) {
            throw new NotConfirmedException();
        }
        if ($user->isAccountLocked() || $currentTime < $user->getAccountLockedUntil()) {
            throw new LockedException();
        }
        if (!$user->isAccountEnabled()) {
            throw new DisabledException();
        }

        if (null !== $user->getAccountExpiresAt() && $currentTime >= $user->getAccountExpiresAt()) {
            throw new AccountExpiredException();
        }
        if (null !== $user->getCredentialsExpireAt() && $currentTime >= $user->getCredentialsExpireAt()) {
            throw new CredentialsExpiredException();
        }
    }
}
