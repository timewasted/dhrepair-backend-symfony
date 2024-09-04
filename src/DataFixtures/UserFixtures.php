<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const string DEFAULT_PASSWORD = 'test123';

    public function load(ObjectManager $manager): void
    {
        $user = $this->getBaseUser()
            ->setUsername('super_admin_user')
            ->setEmail('super_admin_user@example.com')
            ->setRoles([User::ROLE_SUPER_ADMIN]);

        $user->addAuthToken();
        $manager->persist($user);

        $user = $this->getBaseUser()
            ->setUsername('admin_user')
            ->setEmail('admin_user@example.com')
            ->setRoles([User::ROLE_ADMIN]);

        $user->addAuthToken();
        $manager->persist($user);

        $user = $this->getBaseUser()
            ->setUsername('temporary_user')
            ->setEmail('temporary_user@example.com')
            ->setRoles([User::ROLE_TEMPORARY]);

        $user->addAuthToken();
        $manager->persist($user);

        $user = $this->getBaseUser()
            ->setUsername('valid_user')
            ->setEmail('valid_user@example.com')
        ;
        $user->addAuthToken();
        $manager->persist($user);

        $user = $this->getBaseUser()
            ->setUsername('unconfirmed_user')
            ->setEmail('unconfirmed_user@example.com')
            ->setConfirmationToken('confirmation required')
        ;
        $user->addAuthToken();
        $manager->persist($user);

        $user = $this->getBaseUser()
            ->setUsername('locked_user')
            ->setEmail('locked_user@example.com')
            ->setAccountLocked(true)
        ;
        $user->addAuthToken();
        $manager->persist($user);

        $user = $this->getBaseUser()
            ->setUsername('disabled_user')
            ->setEmail('disabled_user@example.com')
            ->setAccountEnabled(false)
        ;
        $user->addAuthToken();
        $manager->persist($user);

        $pastDate = (new \DateTimeImmutable())->sub(new \DateInterval('P1Y'));

        $user = $this->getBaseUser()
            ->setUsername('expired_user')
            ->setEmail('expired_user@example.com')
            ->setAccountExpiresAt($pastDate)
        ;
        $user->addAuthToken();
        $manager->persist($user);

        $user = $this->getBaseUser()
            ->setUsername('credentials_expired_user')
            ->setEmail('credentials_expired_user@example.com')
            ->setCredentialsExpireAt($pastDate)
        ;
        $user->addAuthToken();
        $manager->persist($user);

        $manager->flush();
    }

    private function getBaseUser(): User
    {
        return (new User())
            ->setAccountEnabled(true)
            ->setAccountLocked(false)
            ->setPasswordPlain(self::DEFAULT_PASSWORD)
            ->setRoles([User::ROLE_USER])
        ;
    }
}
