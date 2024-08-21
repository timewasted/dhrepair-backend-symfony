<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserAuthTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserAuthTokenRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'user_id', columns: ['user_id'])]
#[ORM\UniqueConstraint(name: 'auth_token', fields: ['authToken'])]
class UserAuthToken
{
    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'authTokens')]
    #[ORM\JoinColumn(options: ['unsigned' => true])]
    private ?User $user;

    #[ORM\Id]
    #[ORM\Column(length: 255, updatable: false)]
    private string $authToken;

    #[ORM\Column(updatable: false, options: ['default' => 'CURRENT_TIMESTAMP'], generated: 'INSERT')]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->authToken = base64_encode(random_bytes(32));
    }

    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    #[ORM\PrePersist]
    public function onInsert(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
