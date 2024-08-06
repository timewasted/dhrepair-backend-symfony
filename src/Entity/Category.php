<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Index(name: 'parent', fields: ['parent'])]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(options: ['default' => 0])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.category.parent.greater_than_or_equal')]
    private ?int $parent = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'entity.category.name.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'entity.category.name.too_long')]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'entity.category.slug.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'entity.category.slug.too_long')]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(max: 65535, maxMessage: 'entity.category.description.too_long')]
    private ?string $description = null;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $isViewable = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP()'])]
    private ?\DateTimeImmutable $modifiedAt = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class)]
    #[ORM\JoinTable(name: 'category_closure')]
    #[ORM\JoinColumn(name: 'parent', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'child', referencedColumnName: 'id')]
    private Collection $familyTree;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\ManyToMany(targetEntity: Item::class, inversedBy: 'categories')]
    #[ORM\JoinTable(name: 'item_category')]
    private Collection $items;

    public function __construct()
    {
        $this->familyTree = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParent(): ?int
    {
        return $this->parent;
    }

    public function setParent(int $parent): static
    {
        $this->parent = $parent;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isViewable(): ?bool
    {
        return $this->isViewable;
    }

    public function setIsViewable(bool $isViewable): static
    {
        $this->isViewable = $isViewable;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(\DateTimeImmutable $modifiedAt): static
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFamilyTree(): Collection
    {
        return $this->familyTree;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        $this->items->removeElement($item);

        return $this;
    }
}
