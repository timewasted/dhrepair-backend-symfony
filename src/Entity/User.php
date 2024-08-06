<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'username_canonical', fields: ['usernameCanonical'])]
#[ORM\UniqueConstraint(name: 'email_canonical', fields: ['emailCanonical'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const string ROLE_ADMIN = 'ROLE_ADMIN';
    public const string ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const string ROLE_TEMPORARY = 'ROLE_TEMPORARY';
    public const string ROLE_USER = 'ROLE_USER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank(message: 'entity.user.username.not_blank')]
    #[Assert\Length(max: 64, maxMessage: 'entity.user.username.too_long')]
    private ?string $username = null;

    #[ORM\Column(length: 64, unique: true)]
    #[Assert\NotBlank(message: 'entity.user.username_canonical.not_blank')]
    #[Assert\Length(max: 64, maxMessage: 'entity.user.username_canonical.too_long')]
    private ?string $usernameCanonical = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'entity.user.email.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'entity.user.email.too_long')]
    private ?string $email = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'entity.user.email_canonical.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'entity.user.email_canonical.too_long')]
    private ?string $emailCanonical = null;

    /**
     * @var ?resource
     */
    #[ORM\Column(type: Types::BINARY, length: 128)]
    #[Assert\NotBlank(message: 'entity.user.salt.not_blank')]
    #[Assert\Length(max: 128, maxMessage: 'entity.user.salt.too_long')]
    private $salt;

    #[ORM\Column(length: 16)]
    #[Assert\NotBlank(message: 'entity.user.algorithm.not_blank')]
    #[Assert\Length(max: 16, maxMessage: 'entity.user.algorithm.too_long')]
    private ?string $algorithm = null;

    #[ORM\Column]
    #[Assert\GreaterThan(value: 0, message: 'entity.user.work_factor.greater_than')]
    private ?int $workFactor = null;

    #[ORM\Column(length: 128)]
    #[Assert\NotBlank(message: 'entity.user.password.not_blank')]
    #[Assert\Length(max: 128, maxMessage: 'entity.user.password.too_long')]
    private ?string $password = null;

    #[ORM\Column(length: 128, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 128, maxMessage: 'entity.user.confirmation_token.too_long')]
    private ?string $confirmationToken = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP()'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true, options: ['default' => null])]
    private ?\DateTimeImmutable $lastLogin = null;

    #[ORM\Column]
    private ?bool $accountEnabled = null;

    #[ORM\Column]
    private ?bool $accountLocked = null;

    #[ORM\Column(nullable: true, options: ['default' => null])]
    private ?\DateTimeImmutable $accountLockedUntil = null;

    #[ORM\Column(nullable: true, options: ['default' => null])]
    private ?\DateTimeImmutable $accountExpiresAt = null;

    #[ORM\Column(nullable: true, options: ['default' => null])]
    private ?\DateTimeImmutable $credentialsExpireAt = null;

    #[ORM\Column(nullable: true, options: ['default' => null])]
    private ?\DateTimeImmutable $passwordRequestedAt = null;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.user.failed_login_attempts.greater_than_or_equal')]
    private ?int $failedLoginAttempts = null;

    /**
     * @var list<string>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getUsernameCanonical(): ?string
    {
        return $this->usernameCanonical;
    }

    public function setUsernameCanonical(string $usernameCanonical): static
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getEmailCanonical(): ?string
    {
        return $this->emailCanonical;
    }

    public function setEmailCanonical(string $emailCanonical): static
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    /**
     * @return ?resource
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param ?resource $salt
     */
    public function setSalt($salt): static
    {
        $this->salt = $salt;

        return $this;
    }

    public function getAlgorithm(): ?string
    {
        return $this->algorithm;
    }

    public function setAlgorithm(string $algorithm): static
    {
        $this->algorithm = $algorithm;

        return $this;
    }

    public function getWorkFactor(): ?int
    {
        return $this->workFactor;
    }

    public function setWorkFactor(int $workFactor): static
    {
        $this->workFactor = $workFactor;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(string $confirmationToken): static
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeImmutable
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeImmutable $lastLogin): static
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function isAccountEnabled(): ?bool
    {
        return $this->accountEnabled;
    }

    public function setAccountEnabled(bool $accountEnabled): static
    {
        $this->accountEnabled = $accountEnabled;

        return $this;
    }

    public function isAccountLocked(): ?bool
    {
        return $this->accountLocked;
    }

    public function setAccountLocked(bool $accountLocked): static
    {
        $this->accountLocked = $accountLocked;

        return $this;
    }

    public function getAccountLockedUntil(): ?\DateTimeImmutable
    {
        return $this->accountLockedUntil;
    }

    public function setAccountLockedUntil(?\DateTimeImmutable $accountLockedUntil): static
    {
        $this->accountLockedUntil = $accountLockedUntil;

        return $this;
    }

    public function getAccountExpiresAt(): ?\DateTimeImmutable
    {
        return $this->accountExpiresAt;
    }

    public function setAccountExpiresAt(?\DateTimeImmutable $accountExpiresAt): static
    {
        $this->accountExpiresAt = $accountExpiresAt;

        return $this;
    }

    public function getCredentialsExpireAt(): ?\DateTimeImmutable
    {
        return $this->credentialsExpireAt;
    }

    public function setCredentialsExpireAt(?\DateTimeImmutable $credentialsExpireAt): static
    {
        $this->credentialsExpireAt = $credentialsExpireAt;

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTimeImmutable
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTimeImmutable $passwordRequestedAt): static
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    public function getFailedLoginAttempts(): ?int
    {
        return $this->failedLoginAttempts;
    }

    public function setFailedLoginAttempts(?int $failedLoginAttempts): static
    {
        $this->failedLoginAttempts = $failedLoginAttempts;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (empty($roles)) {
            $roles[] = self::ROLE_USER;
        }

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->usernameCanonical;
    }
}
