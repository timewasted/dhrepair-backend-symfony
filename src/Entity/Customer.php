<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\UniqueConstraint(name: 'user_id', fields: ['userId'])]
class Customer
{
    #[ORM\Id]
    #[ORM\Column(options: ['unsigned' => true])]
    #[Assert\GreaterThan(value: 0, message: 'entity.customer.user_id.greater_than')]
    private ?int $userId = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'entity.customer.bill_name.not_blank')]
    #[Assert\Length(max: 100, maxMessage: 'entity.customer.bill_name.too_long')]
    private ?string $billName = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(max: 100, maxMessage: 'entity.customer.bill_company.too_long')]
    private ?string $billCompany = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'entity.customer.bill_address1.not_blank')]
    #[Assert\Length(max: 100, maxMessage: 'entity.customer.bill_address1.too_long')]
    #[Assert\Regex(pattern: '/[\d]/', message: 'entity.customer.bill_address1.requires_digits')]
    private ?string $billAddress1 = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(max: 100, maxMessage: 'entity.customer.bill_address2.too_long')]
    private ?string $billAddress2 = null;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank(message: 'entity.customer.bill_city.not_blank')]
    #[Assert\Length(max: 64, maxMessage: 'entity.customer.bill_city.too_long')]
    private ?string $billCity = null;

    #[ORM\Column(length: 2, options: ['fixed' => true])]
    #[Assert\NotBlank(message: 'entity.customer.bill_state.not_blank')]
    #[Assert\Length(min: 2, max: 2, minMessage: 'entity.customer.bill_state.too_short', maxMessage: 'entity.customer.bill_state.too_long')]
    private ?string $billState = null;

    #[ORM\Column(length: 16)]
    #[Assert\NotBlank(message: 'entity.customer.bill_zip_code.not_blank')]
    #[Assert\Length(max: 16, maxMessage: 'entity.customer.bill_zip_code.too_long')]
    #[Assert\Regex(pattern: '/^[\d]{5,}/', message: 'entity.customer.bill_zip_code.requires_leading_digits_5')]
    private ?string $billZipCode = null;

    #[ORM\Column(length: 2, options: ['default' => 'US', 'fixed' => true])]
    #[Assert\Regex(pattern: '/^US$/', message: 'entity.customer.bill_country.not_us')]
    private ?string $billCountry = 'US';

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'entity.customer.ship_name.not_blank')]
    #[Assert\Length(max: 100, maxMessage: 'entity.customer.ship_name.too_long')]
    private ?string $shipName = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(max: 100, maxMessage: 'entity.customer.ship_company.too_long')]
    private ?string $shipCompany = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'entity.customer.ship_address1.not_blank')]
    #[Assert\Length(max: 100, maxMessage: 'entity.customer.ship_address1.too_long')]
    #[Assert\Regex(pattern: '/[\d]/', message: 'entity.customer.ship_address1.requires_digits')]
    private ?string $shipAddress1 = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(max: 100, maxMessage: 'entity.customer.ship_address2.too_long')]
    private ?string $shipAddress2 = null;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank(message: 'entity.customer.ship_city.not_blank')]
    #[Assert\Length(max: 64, maxMessage: 'entity.customer.ship_city.too_long')]
    private ?string $shipCity = null;

    #[ORM\Column(length: 2, options: ['fixed' => true])]
    #[Assert\NotBlank(message: 'entity.customer.ship_state.not_blank')]
    #[Assert\Length(min: 2, max: 2, minMessage: 'entity.customer.ship_state.too_short', maxMessage: 'entity.customer.ship_state.too_long')]
    private ?string $shipState = null;

    #[ORM\Column(length: 16)]
    #[Assert\NotBlank(message: 'entity.customer.ship_zip_code.not_blank')]
    #[Assert\Length(max: 16, maxMessage: 'entity.customer.ship_zip_code.too_long')]
    #[Assert\Regex(pattern: '/^[\d]{5,}/', message: 'entity.customer.ship_zip_code.requires_leading_digits_5')]
    private ?string $shipZipCode = null;

    #[ORM\Column(length: 2, options: ['default' => 'US', 'fixed' => true])]
    #[Assert\Regex(pattern: '/^US$/', message: 'entity.customer.ship_country.not_us')]
    private ?string $shipCountry = 'US';

    #[ORM\Column(length: 32)]
    #[Assert\NotBlank(message: 'entity.customer.phone_number.not_blank')]
    #[Assert\Length(max: 32, maxMessage: 'entity.customer.phone_number.too_long')]
    #[Assert\Regex(pattern: '/[\d]/', message: 'entity.customer.phone_number.requires_digits')]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'entity.customer.email.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'entity.customer.email.too_long')]
    #[Assert\Email(message: 'entity.customer.email.invalid')]
    private ?string $email = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user = null;

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getBillName(): ?string
    {
        return $this->billName;
    }

    public function setBillName(string $billName): static
    {
        $this->billName = $billName;

        return $this;
    }

    public function getBillCompany(): ?string
    {
        return $this->billCompany;
    }

    public function setBillCompany(string $billCompany): static
    {
        $this->billCompany = $billCompany;

        return $this;
    }

    public function getBillAddress1(): ?string
    {
        return $this->billAddress1;
    }

    public function setBillAddress1(string $billAddress1): static
    {
        $this->billAddress1 = $billAddress1;

        return $this;
    }

    public function getBillAddress2(): ?string
    {
        return $this->billAddress2;
    }

    public function setBillAddress2(string $billAddress2): static
    {
        $this->billAddress2 = $billAddress2;

        return $this;
    }

    public function getBillCity(): ?string
    {
        return $this->billCity;
    }

    public function setBillCity(string $billCity): static
    {
        $this->billCity = $billCity;

        return $this;
    }

    public function getBillState(): ?string
    {
        return $this->billState;
    }

    public function setBillState(string $billState): static
    {
        $this->billState = $billState;

        return $this;
    }

    public function getBillZipCode(): ?string
    {
        return $this->billZipCode;
    }

    public function setBillZipCode(string $billZipCode): static
    {
        $this->billZipCode = $billZipCode;

        return $this;
    }

    public function getBillCountry(): ?string
    {
        return $this->billCountry;
    }

    public function setBillCountry(string $billCountry): static
    {
        $this->billCountry = $billCountry;

        return $this;
    }

    public function getShipName(): ?string
    {
        return $this->shipName;
    }

    public function setShipName(string $shipName): static
    {
        $this->shipName = $shipName;

        return $this;
    }

    public function getShipCompany(): ?string
    {
        return $this->shipCompany;
    }

    public function setShipCompany(string $shipCompany): static
    {
        $this->shipCompany = $shipCompany;

        return $this;
    }

    public function getShipAddress1(): ?string
    {
        return $this->shipAddress1;
    }

    public function setShipAddress1(string $shipAddress1): static
    {
        $this->shipAddress1 = $shipAddress1;

        return $this;
    }

    public function getShipAddress2(): ?string
    {
        return $this->shipAddress2;
    }

    public function setShipAddress2(string $shipAddress2): static
    {
        $this->shipAddress2 = $shipAddress2;

        return $this;
    }

    public function getShipCity(): ?string
    {
        return $this->shipCity;
    }

    public function setShipCity(string $shipCity): static
    {
        $this->shipCity = $shipCity;

        return $this;
    }

    public function getShipState(): ?string
    {
        return $this->shipState;
    }

    public function setShipState(string $shipState): static
    {
        $this->shipState = $shipState;

        return $this;
    }

    public function getShipZipCode(): ?string
    {
        return $this->shipZipCode;
    }

    public function setShipZipCode(string $shipZipCode): static
    {
        $this->shipZipCode = $shipZipCode;

        return $this;
    }

    public function getShipCountry(): ?string
    {
        return $this->shipCountry;
    }

    public function setShipCountry(string $shipCountry): static
    {
        $this->shipCountry = $shipCountry;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
