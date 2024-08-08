<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const string DEFAULT_PASSWORD = 'test123';

    public function __construct(readonly private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->getBaseUser()
            ->setUsername('valid_user')
            ->setEmail('valid_user@example.com')
        );

        $manager->persist($this->getBaseUser()
            ->setUsername('unconfirmed_user')
            ->setEmail('unconfirmed_user@example.com')
            ->setConfirmationToken('confirmation required')
        );

        $manager->persist($this->getBaseUser()
            ->setUsername('locked_user')
            ->setEmail('locked_user@example.com')
            ->setAccountLocked(true)
        );

        $manager->persist($this->getBaseUser()
            ->setUsername('disabled_user')
            ->setEmail('disabled_user@example.com')
            ->setAccountEnabled(false)
        );

        $pastDate = (new \DateTimeImmutable())->sub(new \DateInterval('P1Y'));

        $manager->persist($this->getBaseUser()
            ->setUsername('expired_user')
            ->setEmail('expired_user@example.com')
            ->setAccountExpiresAt($pastDate)
        );

        $manager->persist($this->getBaseUser()
            ->setUsername('credentials_expired_user')
            ->setEmail('credentials_expired_user@example.com')
            ->setCredentialsExpireAt($pastDate)
        );

        $manager->flush();
    }

    private function getBaseUser(): User
    {
        $user = (new User())
            ->setAccountEnabled(true)
            ->setAccountLocked(false)
            ->setRoles(['ROLE_USER'])
        ;

        return $user->setPassword($this->passwordHasher->hashPassword($user, self::DEFAULT_PASSWORD));
    }
}
