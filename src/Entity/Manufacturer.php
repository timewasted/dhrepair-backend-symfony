<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ManufacturerRepository;
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

    #[ORM\OneToOne(mappedBy: 'manufacturer')]
    private ?CostModifier $costModifier = null;

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

    public function getCostModifier(): ?CostModifier
    {
        return $this->costModifier;
    }

    public function setCostModifier(CostModifier $costModifier): static
    {
        // set the owning side of the relation if necessary
        if ($costModifier->getManufacturer() !== $this) {
            $costModifier->setManufacturer($this);
        }

        $this->costModifier = $costModifier;

        return $this;
    }
}
