<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryClosureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryClosureRepository::class)]
#[ORM\UniqueConstraint(name: 'pdc', fields: ['parent', 'child', 'depth'])]
#[ORM\UniqueConstraint(name: 'cpd', fields: ['child', 'parent', 'depth'])]
class CategoryClosure
{
    #[ORM\Id]
    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.category_closure.parent.greater_than_or_equal')]
    private ?int $parent = null;

    #[ORM\Id]
    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.category_closure.child.greater_than_or_equal')]
    private ?int $child = null;

    #[ORM\Id]
    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.category_closure.depth.greater_than_or_equal')]
    private ?int $depth = null;

    #[ORM\ManyToOne(inversedBy: 'familyTree')]
    #[ORM\JoinColumn(name: 'parent')]
    private ?Category $category = null;

    public function getParent(): ?int
    {
        return $this->parent;
    }

    public function setParent(int $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function getChild(): ?int
    {
        return $this->child;
    }

    public function setChild(int $child): static
    {
        $this->child = $child;

        return $this;
    }

    public function getDepth(): ?int
    {
        return $this->depth;
    }

    public function setDepth(int $depth): static
    {
        $this->depth = $depth;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
