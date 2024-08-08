<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ItemImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemImageRepository::class)]
#[ORM\Index(name: 'image_id', fields: ['imageId'])]
#[ORM\UniqueConstraint(name: 'item_image', fields: ['itemId', 'imageId'])]
class ItemImage
{
    #[ORM\Id]
    #[ORM\Column]
    #[Assert\GreaterThan(value: 0, message: 'entity.item_image.item_id.greater_than')]
    private ?int $itemId = null;

    #[ORM\Id]
    #[ORM\Column]
    #[Assert\GreaterThan(value: 0, message: 'entity.item_image.image_id.greater_than')]
    private ?int $imageId = null;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.item_image.position.greater_than_or_equal')]
    private ?int $position = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Item $item = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    private ?Image $image = null;

    public function getItemId(): ?int
    {
        return $this->itemId;
    }

    public function setItemId(int $itemId): static
    {
        $this->itemId = $itemId;

        return $this;
    }

    public function getImageId(): ?int
    {
        return $this->imageId;
    }

    public function setImageId(int $imageId): static
    {
        $this->imageId = $imageId;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;

        return $this;
    }
}
