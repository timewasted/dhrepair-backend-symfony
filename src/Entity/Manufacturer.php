<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ManufacturerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ManufacturerRepository::class)]
#[ORM\UniqueConstraint(name: 'name', fields: ['name'])]
class Manufacturer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(length: 64, unique: true)]
    #[Assert\NotBlank(message: 'entity.manufacturer.name.not_blank')]
    #[Assert\Length(max: 64, maxMessage: 'entity.manufacturer.name.too_long')]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, options: ['default' => '1.00'])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.manufacturer.cost_modifier.greater_than_or_equal')]
    private ?string $costModifier = '1.0';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCostModifier(): ?string
    {
        return $this->costModifier;
    }

    public function setCostModifier(string $costModifier): static
    {
        $this->costModifier = $costModifier;

        return $this;
    }
}
