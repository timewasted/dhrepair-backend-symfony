<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryHierarchyRepository;
use App\Type\LineString;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryHierarchyRepository::class)]
#[ORM\UniqueConstraint(name: 'lft', fields: ['lft'])]
class CategoryHierarchy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    #[Assert\GreaterThan(value: 0, message: 'entity.category_hierarchy.lft.greater_than')]
    private ?int $lft = null;

    #[ORM\Column]
    #[Assert\GreaterThan(value: 0, message: 'entity.category_hierarchy.rgt.greater_than')]
    private ?int $rgt = null;

    #[ORM\Column(type: 'linestring')]
    #[Assert\NotBlank(message: 'entity.category_hierarchy.sets.not_blank')]
    private ?LineString $sets = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLft(): ?int
    {
        return $this->lft;
    }

    public function setLft(int $lft): static
    {
        $this->lft = $lft;

        return $this;
    }

    public function getRgt(): ?int
    {
        return $this->rgt;
    }

    public function setRgt(int $rgt): static
    {
        $this->rgt = $rgt;

        return $this;
    }

    public function getSets(): ?LineString
    {
        return $this->sets;
    }

    public function setSets(LineString $sets): static
    {
        $this->sets = $sets;

        return $this;
    }
}
