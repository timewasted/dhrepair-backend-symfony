<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(length: 128)]
    #[Assert\NotBlank(message: 'entity.user.password.not_blank')]
    #[Assert\Length(max: 128, maxMessage: 'entity.user.password.too_long')]
    private ?string $password = null;

    #[ORM\Column(length: 128, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 128, maxMessage: 'entity.user.confirmation_token.too_long')]
    private ?string $confirmationToken = null;

    #[ORM\Column(insertable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
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

    /**
     * @var Collection<int, UserAuthToken>
     */
    #[ORM\OneToMany(targetEntity: UserAuthToken::class, mappedBy: 'user', cascade: ['persist'], orphanRemoval: true)]
    private Collection $authTokens;

    public function __construct()
    {
        $this->authTokens = new ArrayCollection();
    }

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

    public function getLastLogin(): ?\DateTimeImmutable
    {
        return $this->lastLogin;
    }

    public function setLastLogin(\DateTimeImmutable $lastLogin): static
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

    /**
     * @return Collection<int, UserAuthToken>
     */
    public function getAuthTokens(): Collection
    {
        return $this->authTokens;
    }

    public function addAuthToken(): UserAuthToken
    {
        $authToken = new UserAuthToken($this);
        $this->authTokens->add($authToken);

        return $authToken;
    }

    public function removeAllAuthTokens(): static
    {
        $this->authTokens->clear();

        return $this;
    }

    public function removeAuthToken(UserAuthToken $authToken): static
    {
        $this->authTokens->removeElement($authToken);

        return $this;
    }
}
