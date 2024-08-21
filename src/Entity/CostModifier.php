<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CostModifierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CostModifierRepository::class)]
class CostModifier
{
    #[ORM\Id]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $manufacturerId = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, options: ['default' => '1.0'])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.cost_modifier.modifier.greater_than_or_equal')]
    private ?string $modifier = '1.0';

    #[ORM\OneToOne(inversedBy: 'costModifier')]
    private ?Manufacturer $manufacturer = null;

    public function getManufacturerId(): ?int
    {
        return $this->manufacturerId;
    }

    public function setManufacturerId(int $manufacturerId): static
    {
        $this->manufacturerId = $manufacturerId;

        return $this;
    }

    public function getModifier(): ?string
    {
        return $this->modifier;
    }

    public function setModifier(string $modifier): static
    {
        $this->modifier = $modifier;

        return $this;
    }

    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(Manufacturer $manufacturer): static
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }
}
