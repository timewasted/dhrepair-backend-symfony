<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: '`order_info`')]
#[ORM\Index(name: 'username', fields: ['username'])]
#[ORM\Index(name: 'order_number', fields: ['orderNumber'])]
#[ORM\Index(name: 'created_at', fields: ['createdAt'])]
final class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 255, maxMessage: 'entity.order.username.too_long')]
    private ?string $username = null;

    #[ORM\Column(length: 24, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 24, maxMessage: 'entity.order.order_number.too_long')]
    private ?string $orderNumber = null;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank(message: 'entity.order.receipt_id.not_blank')]
    #[Assert\Length(max: 64, maxMessage: 'entity.order.receipt_id.too_long')]
    private ?string $receiptId = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'entity.order.bill_name.not_blank')]
    #[Assert\Length(max: 100, maxMessage: 'entity.order.bill_name.too_long')]
    private ?string $billName = null;

    #[ORM\Column(length: 100, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 100, maxMessage: 'entity.order.bill_company.too_long')]
    private ?string $billCompany = null;

    #[ORM\Column(name: 'bill_address_1', length: 100)]
    #[Assert\NotBlank(message: 'entity.order.bill_address1.not_blank')]
    #[Assert\Length(max: 100, maxMessage: 'entity.order.bill_address1.too_long')]
    #[Assert\Regex(pattern: '/[\d]/', message: 'entity.order.bill_address1.requires_digits')]
    private ?string $billAddress1 = null;

    #[ORM\Column(name: 'bill_address_2', length: 100, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 100, maxMessage: 'entity.order.bill_address2.too_long')]
    private ?string $billAddress2 = null;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank(message: 'entity.order.bill_city.not_blank')]
    #[Assert\Length(max: 64, maxMessage: 'entity.order.bill_city.too_long')]
    private ?string $billCity = null;

    #[ORM\Column(length: 2, options: ['fixed' => true])]
    #[Assert\NotBlank(message: 'entity.order.bill_state.not_blank')]
    #[Assert\Length(min: 2, max: 2, minMessage: 'entity.order.bill_state.too_short', maxMessage: 'entity.order.bill_state.too_long')]
    private ?string $billState = null;

    #[ORM\Column(length: 16)]
    #[Assert\NotBlank(message: 'entity.order.bill_zip_code.not_blank')]
    #[Assert\Length(max: 16, maxMessage: 'entity.order.bill_zip_code.too_long')]
    #[Assert\Regex(pattern: '/^[\d]{5,}/', message: 'entity.order.bill_zip_code.requires_leading_digits_5')]
    private ?string $billZipCode = null;

    #[ORM\Column(length: 2, options: ['default' => 'US', 'fixed' => true])]
    #[Assert\Regex(pattern: '/^US$/', message: 'entity.order.bill_country.not_us')]
    private ?string $billCountry = 'US';

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'entity.order.ship_name.not_blank')]
    #[Assert\Length(max: 100, maxMessage: 'entity.order.ship_name.too_long')]
    private ?string $shipName = null;

    #[ORM\Column(length: 100, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 100, maxMessage: 'entity.order.ship_company.too_long')]
    private ?string $shipCompany = null;

    #[ORM\Column(name: 'ship_address_1', length: 100)]
    #[Assert\NotBlank(message: 'entity.order.ship_address1.not_blank')]
    #[Assert\Length(max: 100, maxMessage: 'entity.order.ship_address1.too_long')]
    #[Assert\Regex(pattern: '/[\d]/', message: 'entity.order.ship_address1.requires_digits')]
    private ?string $shipAddress1 = null;

    #[ORM\Column(name: 'ship_address_2', length: 100, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 100, maxMessage: 'entity.order.ship_address2.too_long')]
    private ?string $shipAddress2 = null;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank(message: 'entity.order.ship_city.not_blank')]
    #[Assert\Length(max: 64, maxMessage: 'entity.order.ship_city.too_long')]
    private ?string $shipCity = null;

    #[ORM\Column(length: 2, options: ['fixed' => true])]
    #[Assert\NotBlank(message: 'entity.order.ship_state.not_blank')]
    #[Assert\Length(min: 2, max: 2, minMessage: 'entity.order.ship_state.too_short', maxMessage: 'entity.order.ship_state.too_long')]
    private ?string $shipState = null;

    #[ORM\Column(length: 16)]
    #[Assert\NotBlank(message: 'entity.order.ship_zip_code.not_blank')]
    #[Assert\Length(max: 16, maxMessage: 'entity.order.ship_zip_code.too_long')]
    #[Assert\Regex(pattern: '/^[\d]{5,}/', message: 'entity.order.ship_zip_code.requires_leading_digits_5')]
    private ?string $shipZipCode = null;

    #[ORM\Column(length: 2, options: ['default' => 'US', 'fixed' => true])]
    #[Assert\Regex(pattern: '/^US$/', message: 'entity.order.ship_country.not_us')]
    private ?string $shipCountry = 'US';

    #[ORM\Column(length: 32)]
    #[Assert\NotBlank(message: 'entity.order.phone_number.not_blank')]
    #[Assert\Length(max: 32, maxMessage: 'entity.order.phone_number.too_long')]
    #[Assert\Regex(pattern: '/[\d]/', message: 'entity.order.phone_number.requires_digits')]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'entity.order.email.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'entity.order.email.too_long')]
    #[Assert\Email(message: 'entity.order.email.invalid')]
    private ?string $email = null;

    #[ORM\Column(length: 512, nullable: true, options: ['default' => null])]
    #[Assert\Length(max: 512, maxMessage: 'entity.order.comments.too_long')]
    private ?string $comments = null;

    #[ORM\Column(options: ['unsigned' => true])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.order.subtotal.greater_than_or_equal')]
    private ?int $subtotal = null;

    #[ORM\Column(options: ['unsigned' => true])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.order.tax.greater_than_or_equal')]
    private ?int $tax = null;

    #[ORM\Column(options: ['unsigned' => true])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.order.shipping.greater_than_or_equal')]
    private ?int $shipping = null;

    #[ORM\Column]
    private ?bool $refundUnusedShipping = null;

    #[ORM\Column(updatable: false, options: ['default' => 'CURRENT_TIMESTAMP'], generated: 'INSERT')]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, TransactionLog>
     */
    #[ORM\OneToMany(targetEntity: TransactionLog::class, mappedBy: 'orderInfo', cascade: ['persist'])]
    private Collection $transactionLog;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'orderInfo', cascade: ['persist'])]
    private Collection $items;

    public function __construct()
    {
        $this->transactionLog = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): Order
    {
        $this->username = $username;

        return $this;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(?string $orderNumber): Order
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getReceiptId(): ?string
    {
        return $this->receiptId;
    }

    public function setReceiptId(string $receiptId): Order
    {
        $this->receiptId = $receiptId;

        return $this;
    }

    public function getBillName(): ?string
    {
        return $this->billName;
    }

    public function setBillName(string $billName): Order
    {
        $this->billName = $billName;

        return $this;
    }

    public function getBillCompany(): ?string
    {
        return $this->billCompany;
    }

    public function setBillCompany(string $billCompany): Order
    {
        $this->billCompany = $billCompany;

        return $this;
    }

    public function getBillAddress1(): ?string
    {
        return $this->billAddress1;
    }

    public function setBillAddress1(string $billAddress1): Order
    {
        $this->billAddress1 = $billAddress1;

        return $this;
    }

    public function getBillAddress2(): ?string
    {
        return $this->billAddress2;
    }

    public function setBillAddress2(string $billAddress2): Order
    {
        $this->billAddress2 = $billAddress2;

        return $this;
    }

    public function getBillCity(): ?string
    {
        return $this->billCity;
    }

    public function setBillCity(string $billCity): Order
    {
        $this->billCity = $billCity;

        return $this;
    }

    public function getBillState(): ?string
    {
        return $this->billState;
    }

    public function setBillState(string $billState): Order
    {
        $this->billState = $billState;

        return $this;
    }

    public function getBillZipCode(): ?string
    {
        return $this->billZipCode;
    }

    public function setBillZipCode(string $billZipCode): Order
    {
        $this->billZipCode = $billZipCode;

        return $this;
    }

    public function getBillCountry(): ?string
    {
        return $this->billCountry;
    }

    public function setBillCountry(string $billCountry): Order
    {
        $this->billCountry = $billCountry;

        return $this;
    }

    public function getShipName(): ?string
    {
        return $this->shipName;
    }

    public function setShipName(string $shipName): Order
    {
        $this->shipName = $shipName;

        return $this;
    }

    public function getShipCompany(): ?string
    {
        return $this->shipCompany;
    }

    public function setShipCompany(string $shipCompany): Order
    {
        $this->shipCompany = $shipCompany;

        return $this;
    }

    public function getShipAddress1(): ?string
    {
        return $this->shipAddress1;
    }

    public function setShipAddress1(string $shipAddress1): Order
    {
        $this->shipAddress1 = $shipAddress1;

        return $this;
    }

    public function getShipAddress2(): ?string
    {
        return $this->shipAddress2;
    }

    public function setShipAddress2(string $shipAddress2): Order
    {
        $this->shipAddress2 = $shipAddress2;

        return $this;
    }

    public function getShipCity(): ?string
    {
        return $this->shipCity;
    }

    public function setShipCity(string $shipCity): Order
    {
        $this->shipCity = $shipCity;

        return $this;
    }

    public function getShipState(): ?string
    {
        return $this->shipState;
    }

    public function setShipState(string $shipState): Order
    {
        $this->shipState = $shipState;

        return $this;
    }

    public function getShipZipCode(): ?string
    {
        return $this->shipZipCode;
    }

    public function setShipZipCode(string $shipZipCode): Order
    {
        $this->shipZipCode = $shipZipCode;

        return $this;
    }

    public function getShipCountry(): ?string
    {
        return $this->shipCountry;
    }

    public function setShipCountry(string $shipCountry): Order
    {
        $this->shipCountry = $shipCountry;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): Order
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): Order
    {
        $this->email = $email;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): Order
    {
        $this->comments = $comments;

        return $this;
    }

    public function getSubtotal(): ?int
    {
        return $this->subtotal;
    }

    public function setSubtotal(int $subtotal): Order
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getTax(): ?int
    {
        return $this->tax;
    }

    public function setTax(int $tax): Order
    {
        $this->tax = $tax;

        return $this;
    }

    public function getShipping(): ?int
    {
        return $this->shipping;
    }

    public function setShipping(int $shipping): Order
    {
        $this->shipping = $shipping;

        return $this;
    }

    public function isRefundUnusedShipping(): ?bool
    {
        return $this->refundUnusedShipping;
    }

    public function setRefundUnusedShipping(bool $refundUnusedShipping): Order
    {
        $this->refundUnusedShipping = $refundUnusedShipping;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, TransactionLog>
     */
    public function getTransactionLog(): Collection
    {
        return $this->transactionLog;
    }

    public function addTransactionLog(TransactionLog $transactionLog): Order
    {
        if (!$this->transactionLog->contains($transactionLog)) {
            $this->transactionLog->add($transactionLog);
            $transactionLog->setOrderInfo($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): Order
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrderInfo($this);
        }

        return $this;
    }

    #[ORM\PrePersist]
    public function onInsert(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
