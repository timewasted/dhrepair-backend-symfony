<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
#[ORM\Index(name: 'item_id', fields: ['itemId'])]
#[ORM\UniqueConstraint(name: 'user_item', fields: ['userId', 'itemId'])]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\GreaterThan(value: 0, message: 'entity.cart_item.user_id.greater_than')]
    private ?int $userId = null;

    #[ORM\Column]
    #[Assert\GreaterThan(value: 0, message: 'entity.cart_item.item_id.greater_than')]
    private ?int $itemId = null;

    #[ORM\Column]
    #[Assert\GreaterThan(value: 0, message: 'entity.cart_item.quantity.greater_than')]
    private ?int $quantity = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Item $item = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getItemId(): ?int
    {
        return $this->itemId;
    }

    public function setItemId(int $itemId): static
    {
        $this->itemId = $itemId;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(Item $item): static
    {
        $this->item = $item;

        return $this;
    }
}
