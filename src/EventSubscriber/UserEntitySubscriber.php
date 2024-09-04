<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::prePersist, entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, entity: User::class)]
readonly class UserEntitySubscriber
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function prePersist(User $user): void
    {
        $this->handleChanges($user);
    }

    public function preUpdate(User $user): void
    {
        $this->handleChanges($user);
    }

    private function canonicalize(string $input): string
    {
        return mb_convert_case($input, MB_CASE_LOWER, 'UTF-8');
    }

    private function handleChanges(User $user): void
    {
        if (null !== ($username = $user->getUsername())) {
            $user->setUsernameCanonical($this->canonicalize($username));
        }
        if (null !== ($username = $user->getEmail())) {
            $user->setEmailCanonical($this->canonicalize($username));
        }
        if (null !== ($password = $user->getPasswordPlain())) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setPasswordPlain(null);
        }
    }
}
