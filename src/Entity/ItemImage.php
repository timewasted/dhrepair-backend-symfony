<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ItemImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemImageRepository::class)]
#[ORM\Index(name: 'image_id', columns: ['image_id'])]
#[ORM\UniqueConstraint(name: 'item_image', columns: ['item_id', 'image_id'])]
class ItemImage
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'itemImages')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Item $item = null;

    #[ORM\Id]
    #[ORM\ManyToOne(cascade: ['persist'], fetch: 'EAGER', inversedBy: 'itemImages')]
    private ?Image $image = null;

    #[ORM\Column(options: ['unsigned' => true])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.item_image.position.greater_than_or_equal')]
    private ?int $position = null;

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(Item $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(Image $image): static
    {
        $this->image = $image;

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
}
