<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AccessLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AccessLogRepository::class)]
class AccessLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 46)]
    #[Assert\NotBlank(message: 'entity.access_log.ip.not_blank')]
    #[Assert\Length(max: 46, maxMessage: 'entity.access_log.ip.too_long')]
    private ?string $ip = null;

    #[ORM\Column(length: 64, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 64, maxMessage: 'entity.access_log.username.too_long')]
    private ?string $username = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $timestamp = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'entity.access_log.uri.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'entity.access_log.uri.too_long')]
    private ?string $uri = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'entity.access_log.title.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'entity.access_log.title.too_long')]
    private ?string $title = null;

    #[ORM\Column(length: 1536, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 1536, maxMessage: 'entity.access_log.referer.too_long')]
    private ?string $referer = null;

    #[ORM\Column(length: 255, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 255, maxMessage: 'entity.access_log.referer_title.too_long')]
    private ?string $refererTitle = null;

    #[ORM\Column(length: 1536, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 1536, maxMessage: 'entity.access_log.browser.too_long')]
    private ?string $browser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeImmutable $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(string $uri): static
    {
        $this->uri = $uri;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }

    public function setReferer(?string $referer): static
    {
        $this->referer = $referer;

        return $this;
    }

    public function getRefererTitle(): ?string
    {
        return $this->refererTitle;
    }

    public function setRefererTitle(?string $refererTitle): static
    {
        $this->refererTitle = $refererTitle;

        return $this;
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function setBrowser(?string $browser): static
    {
        $this->browser = $browser;

        return $this;
    }
}
