<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\Index(name: 'image', fields: ['image'])]
#[ORM\UniqueConstraint(name: 'image_hash', fields: ['imageHash'])]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'entity.image.image.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'entity.image.image.too_long')]
    private ?string $image = null;

    #[ORM\Column(length: 128, unique: true)]
    #[Assert\NotBlank(message: 'entity.image.image_hash.not_blank')]
    #[Assert\Length(max: 128, maxMessage: 'entity.image.image_hash.too_long')]
    private ?string $imageHash = null;

    #[ORM\Column(length: 255, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 255, maxMessage: 'entity.image.title.too_long')]
    private ?string $title = null;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.image.width.greater_than_or_equal')]
    private ?int $width = null;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.image.height.greater_than_or_equal')]
    private ?int $height = null;

    #[ORM\Column(nullable: true, options: ['default' => null])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.image.thumb_width.greater_than_or_equal')]
    private ?int $thumbWidth = null;

    #[ORM\Column(nullable: true, options: ['default' => null])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.image.thumb_height.greater_than_or_equal')]
    private ?int $thumbHeight = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP()'])]
    private ?\DateTimeImmutable $addedAt = null;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\ManyToMany(targetEntity: Item::class, mappedBy: 'images')]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getImageHash(): ?string
    {
        return $this->imageHash;
    }

    public function setImageHash(string $imageHash): static
    {
        $this->imageHash = $imageHash;

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

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getThumbWidth(): ?int
    {
        return $this->thumbWidth;
    }

    public function setThumbWidth(?int $thumbWidth): static
    {
        $this->thumbWidth = $thumbWidth;

        return $this;
    }

    public function getThumbHeight(): ?int
    {
        return $this->thumbHeight;
    }

    public function setThumbHeight(?int $thumbHeight): static
    {
        $this->thumbHeight = $thumbHeight;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeImmutable $addedAt): static
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->addImage($this);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        if ($this->items->removeElement($item)) {
            $item->removeImage($this);
        }

        return $this;
    }
}
