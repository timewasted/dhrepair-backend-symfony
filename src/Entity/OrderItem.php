<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ORM\Index(name: 'order_id', fields: ['orderId'])]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\GreaterThan(value: 0, message: 'entity.order_item.order_id.greater_than')]
    private ?int $orderId = null;

    #[ORM\Column]
    #[Assert\GreaterThan(value: 0, message: 'entity.order_item.quantity.greater_than')]
    private ?int $quantity = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'entity.order_item.name.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'entity.order_item.name.too_long')]
    private ?string $name = null;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank(message: 'entity.order_item.sku.not_blank')]
    #[Assert\Length(max: 64, maxMessage: 'entity.order_item.sku.too_long')]
    private ?string $sku = null;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.order_item.cost.greater_than_or_equal')]
    private ?int $cost = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'order_id')]
    private ?Order $orderInfo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): static
    {
        $this->orderId = $orderId;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): static
    {
        $this->sku = $sku;

        return $this;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(int $cost): static
    {
        $this->cost = $cost;

        return $this;
    }

    public function getOrderInfo(): ?Order
    {
        return $this->orderInfo;
    }

    public function setOrderInfo(?Order $orderInfo): static
    {
        $this->orderInfo = $orderInfo;

        return $this;
    }
}
