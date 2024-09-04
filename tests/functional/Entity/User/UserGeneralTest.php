<?php

declare(strict_types=1);

namespace App\Tests\functional\Entity\User;

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
}
