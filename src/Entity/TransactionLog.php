<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TransactionLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransactionLogRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'transaction_id', fields: ['transactionId'])]
#[ORM\Index(name: 'order_id', fields: ['orderId'])]
class TransactionLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\GreaterThan(value: 0, message: 'entity.transaction_log.order_id.greater_than')]
    private ?int $orderId = null;

    #[ORM\Column(length: 32, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 32, maxMessage: 'entity.transaction_log.referenced_id.too_long')]
    private ?string $referencedId = null;

    #[ORM\Column(length: 32)]
    #[Assert\NotBlank(message: 'entity.transaction_log.transaction_id.not_blank')]
    #[Assert\Length(max: 32, maxMessage: 'entity.transaction_log.transaction_id.too_long')]
    private ?string $transactionId = null;

    #[ORM\Column(length: 32)]
    #[Assert\NotBlank(message: 'entity.transaction_log.action.not_blank')]
    #[Assert\Length(max: 32, maxMessage: 'entity.transaction_log.action.too_long')]
    private ?string $action = null;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.transaction_log.amount.greater_than_or_equal')]
    private ?int $amount = null;

    #[ORM\Column]
    private ?bool $isSuccess = null;

    #[ORM\Column(nullable: true, options: ['default' => null])]
    private ?bool $isAvsSuccess = null;

    #[ORM\Column(nullable: true, options: ['default' => null])]
    private ?bool $isCvv2Success = null;

    #[ORM\Column(updatable: false, options: ['default' => 'CURRENT_TIMESTAMP'], generated: 'INSERT')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'transactionLog')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id')]
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

    public function getReferencedId(): ?string
    {
        return $this->referencedId;
    }

    public function setReferencedId(?string $referencedId): static
    {
        $this->referencedId = $referencedId;

        return $this;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function setTransactionId(string $transactionId): static
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function isSuccess(): ?bool
    {
        return $this->isSuccess;
    }

    public function setIsSuccess(bool $isSuccess): static
    {
        $this->isSuccess = $isSuccess;

        return $this;
    }

    public function isAvsSuccess(): ?bool
    {
        return $this->isAvsSuccess;
    }

    public function setIsAvsSuccess(?bool $isAvsSuccess): static
    {
        $this->isAvsSuccess = $isAvsSuccess;

        return $this;
    }

    public function isCvv2Success(): ?bool
    {
        return $this->isCvv2Success;
    }

    public function setIsCvv2Success(?bool $isCvv2Success): static
    {
        $this->isCvv2Success = $isCvv2Success;

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

    public function getOrderInfo(): ?Order
    {
        return $this->orderInfo;
    }

    public function setOrderInfo(?Order $orderInfo): static
    {
        $this->orderInfo = $orderInfo;

        return $this;
    }

    #[ORM\PrePersist]
    public function onInsert(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
