<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PageContentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PageContentRepository::class)]
class PageContent
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'entity.page_content.page.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'entity.page_content.page.too_long')]
    private ?string $page = null;

    #[ORM\Column(length: 255, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 255, maxMessage: 'entity.page_content.title.too_long')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'entity.page_content.text.not_blank')]
    #[Assert\Length(max: 16777215, maxMessage: 'entity.page_content.text.too_long')]
    private ?string $content = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $modifiedAt = null;

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function setPage(string $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(\DateTimeImmutable $modifiedAt): static
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }
}
