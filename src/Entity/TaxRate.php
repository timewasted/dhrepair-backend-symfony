<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TaxRateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaxRateRepository::class)]
#[ORM\UniqueConstraint(name: 'state', fields: ['state'])]
class TaxRate
{
    #[ORM\Id]
    #[ORM\Column(length: 2, unique: true, options: ['fixed' => true])]
    #[Assert\NotBlank(message: 'entity.tax_rate.state.not_blank')]
    #[Assert\Length(min: 2, max: 2, minMessage: 'entity.tax_rate.state.too_short', maxMessage: 'entity.tax_rate.state.too_long')]
    private ?string $state = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 3)]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.tax_rate.rate.greater_than_or_equal')]
    private ?string $rate = null;

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(string $rate): static
    {
        $this->rate = $rate;

        return $this;
    }
}
