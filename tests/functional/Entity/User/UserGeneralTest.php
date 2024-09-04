<?php

declare(strict_types=1);

namespace App\Tests\functional\Entity\User;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserGeneralTest extends KernelTestCase
{
    private ObjectManager $entityManager;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $container = self::getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function testCreateTemporaryUser(): void
    {
        $authToken = $this->userRepository->createTemporaryUser();
        $user = $authToken->getUser();
        $this->assertNotNull($user);
        $user = $this->userRepository->find($user->getId());
        $this->assertNotNull($user);

        $this->assertSame($authToken->getUser(), $user);
        $this->assertMatchesRegularExpression('/^temp_[A-Fa-f\d]{32}$/', (string) $user->getUsername());
        $this->assertMatchesRegularExpression('/^temp_[A-Fa-f\d]{32}$/', (string) $user->getEmail());
        $this->assertTrue($user->isAccountEnabled());
        $this->assertFalse($user->isAccountLocked());
        $this->assertSame([User::ROLE_TEMPORARY], $user->getRoles());
    }

    public function testEntityInserted(): void
    {
        $username = 'THIS iš ä țèşť';
        $email = 'TEST@EXAMPLE.COM';
        $user = (new User())
            ->setUsername($username)
            ->setEmail($email)
            ->setPasswordPlain('test123')
            ->setAccountEnabled(false)
            ->setAccountLocked(false)
        ;
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertSame($username, $user->getUsername());
        $this->assertSame(mb_convert_case($username, MB_CASE_LOWER, 'UTF-8'), $user->getUsernameCanonical());
        $this->assertSame($email, $user->getEmail());
        $this->assertSame(mb_convert_case($email, MB_CASE_LOWER, 'UTF-8'), $user->getEmailCanonical());
        $this->assertNotNull($user->getPassword());
        $this->assertNull($user->getPasswordPlain());
        $this->assertEqualsWithDelta((new \DateTimeImmutable())->getTimestamp(), $user->getCreatedAt()?->getTimestamp(), 2);
        $this->assertSame([User::ROLE_USER], $user->getRoles());
    }

    public function testEntityUpdated(): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => 'valid_user']);
        $this->assertNotNull($user);
        /** @var \DateTimeImmutable $userCreatedAt */
        $userCreatedAt = $user->getCreatedAt();

        $username = 'THIS iš ä țèşť';
        $email = 'TEST@EXAMPLE.COM';
        $user
            ->setUsername($username)
            ->setEmail($email)
            ->setPasswordPlain('test123')
            ->setAccountEnabled(false)
            ->setAccountLocked(false)
        ;
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertSame($username, $user->getUsername());
        $this->assertSame(mb_convert_case($username, MB_CASE_LOWER, 'UTF-8'), $user->getUsernameCanonical());
        $this->assertSame($email, $user->getEmail());
        $this->assertSame(mb_convert_case($email, MB_CASE_LOWER, 'UTF-8'), $user->getEmailCanonical());
        $this->assertNotNull($user->getPassword());
        $this->assertNull($user->getPasswordPlain());
        $this->assertSame($userCreatedAt->getTimestamp(), $user->getCreatedAt()?->getTimestamp());
    }

    public function testUpgradePassword(): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => 'valid_user']);
        $this->assertNotNull($user);
        $newPassword = password_hash(UserFixtures::DEFAULT_PASSWORD.'!', PASSWORD_DEFAULT);
        $this->userRepository->upgradePassword($user, $newPassword);

        $this->assertSame($newPassword, $user->getPassword());
    }
}
