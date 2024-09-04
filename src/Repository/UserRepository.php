<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserAuthToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function createTemporaryUser(): UserAuthToken
    {
        $tempString = 'temp_'.bin2hex(random_bytes(16));
        $user = (new User())
            ->setUsername($tempString)
            ->setEmail($tempString)
            ->setPasswordPlain(random_bytes(16))
            ->setAccountEnabled(true)
            ->setAccountLocked(false)
            ->setRoles([User::ROLE_TEMPORARY])
        ;
        $authToken = $user->addAuthToken();
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $authToken;
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function incrementFailedLoginCount(string $username): void
    {
        $this->getEntityManager()->createQueryBuilder()
            ->update(User::class, 'u')
            ->set('u.failedLoginAttempts', 'u.failedLoginAttempts + 1')
            ->where('u.usernameCanonical = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->execute()
        ;
    }
}
