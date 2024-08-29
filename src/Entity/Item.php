<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'manufacturer_id', fields: ['manufacturerId'])]
#[ORM\Index(name: 'availability_id', fields: ['availabilityId'])]
#[ORM\Index(name: 'is_product', fields: ['isProduct'])]
#[ORM\Index(name: 'is_viewable', fields: ['isViewable'])]
#[ORM\Index(name: 'is_purchasable', fields: ['isPurchasable'])]
#[ORM\Index(name: 'is_special', fields: ['isSpecial'])]
#[ORM\Index(name: 'is_new', fields: ['isNew'])]
#[ORM\Index(name: 'charge_shipping', fields: ['chargeShipping'])]
#[ORM\Index(name: 'is_free_shipping', fields: ['isFreeShipping'])]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'entity.item.name.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'entity.item.name.too_long')]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'entity.item.slug.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'entity.item.slug.too_long')]
    private ?string $slug = null;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank(message: 'entity.item.sku.not_blank')]
    #[Assert\Length(max: 64, maxMessage: 'entity.item.sku.too_long')]
    private ?string $sku = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'entity.item.description.not_blank')]
    #[Assert\Length(max: 65535, maxMessage: 'entity.item.description.too_long')]
    private ?string $description = null;

    #[ORM\Column(options: ['default' => 0, 'unsigned' => true])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.item.manufacturer_id.greater_than_or_equal')]
    private ?int $manufacturerId = 0;

    #[ORM\Column(options: ['default' => 0, 'unsigned' => true])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.item.cost.greater_than_or_equal')]
    private ?int $cost = 0;

    #[ORM\Column(options: ['default' => -1])]
    #[Assert\GreaterThanOrEqual(value: -1, message: 'entity.item.quantity.greater_than_or_equal')]
    private ?int $quantity = -1;

    #[ORM\Column(options: ['unsigned' => true])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.item.availability_id.greater_than_or_equal')]
    private ?int $availabilityId = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, options: ['default' => '0.00'])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.item.weight.greater_than_or_equal')]
    private ?string $weight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, options: ['default' => '0.00'])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.item.length.greater_than_or_equal')]
    private ?string $length = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, options: ['default' => '0.00'])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.item.width.greater_than_or_equal')]
    private ?string $width = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, options: ['default' => '0.00'])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'entity.item.height.greater_than_or_equal')]
    private ?string $height = '0.00';

    #[ORM\Column(options: ['default' => true])]
    private ?bool $isProduct = true;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $isViewable = true;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $isPurchasable = true;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isSpecial = false;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $isNew = true;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $chargeTax = true;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $chargeShipping = true;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isFreeShipping = false;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $freightQuoteRequired = false;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'], generated: 'ALWAYS')]
    private ?\DateTimeImmutable $modifiedAt = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    private ?Manufacturer $manufacturer = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    private ?Availability $availability = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'items')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private Collection $categories;

    /**
     * @var Collection<int, ItemImage>
     */
    #[ORM\OneToMany(targetEntity: ItemImage::class, mappedBy: 'item', cascade: ['persist'], fetch: 'EAGER')]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $itemImages;

    /**
     * @var array<int, Image>
     */
    private array $images = [];

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->itemImages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): static
    {
        $this->sku = $sku;

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

    public function getManufacturerId(): ?int
    {
        return $this->manufacturerId;
    }

    public function setManufacturerId(int $manufacturerId): static
    {
        $this->manufacturerId = $manufacturerId;

        return $this;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(int $cost): static
    {
        $this->cost = $cost;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getAvailabilityId(): ?int
    {
        return $this->availabilityId;
    }

    public function setAvailabilityId(int $availabilityId): static
    {
        $this->availabilityId = $availabilityId;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getLength(): ?string
    {
        return $this->length;
    }

    public function setLength(string $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(string $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(string $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function isProduct(): ?bool
    {
        return $this->isProduct;
    }

    public function setIsProduct(bool $isProduct): static
    {
        $this->isProduct = $isProduct;

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

    public function isPurchasable(): ?bool
    {
        return $this->isPurchasable;
    }

    public function setIsPurchasable(bool $isPurchasable): static
    {
        $this->isPurchasable = $isPurchasable;

        return $this;
    }

    public function isSpecial(): ?bool
    {
        return $this->isSpecial;
    }

    public function setIsSpecial(bool $isSpecial): static
    {
        $this->isSpecial = $isSpecial;

        return $this;
    }

    public function isNew(): ?bool
    {
        return $this->isNew;
    }

    public function setIsNew(bool $isNew): static
    {
        $this->isNew = $isNew;

        return $this;
    }

    public function isChargeTax(): ?bool
    {
        return $this->chargeTax;
    }

    public function setChargeTax(bool $chargeTax): static
    {
        $this->chargeTax = $chargeTax;

        return $this;
    }

    public function isChargeShipping(): ?bool
    {
        return $this->chargeShipping;
    }

    public function setChargeShipping(bool $chargeShipping): static
    {
        $this->chargeShipping = $chargeShipping;

        return $this;
    }

    public function isFreeShipping(): ?bool
    {
        return $this->isFreeShipping;
    }

    public function setIsFreeShipping(bool $isFreeShipping): static
    {
        $this->isFreeShipping = $isFreeShipping;

        return $this;
    }

    public function isFreightQuoteRequired(): ?bool
    {
        return $this->freightQuoteRequired;
    }

    public function setFreightQuoteRequired(bool $freightQuoteRequired): static
    {
        $this->freightQuoteRequired = $freightQuoteRequired;

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

    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?Manufacturer $manufacturer): static
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getAvailability(): ?Availability
    {
        return $this->availability;
    }

    public function setAvailability(?Availability $availability): static
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, ItemImage>
     */
    public function getItemImages(): Collection
    {
        return $this->itemImages;
    }

    /**
     * @return array<int, Image>
     */
    public function getImages(): array
    {
        return $this->images;
    }

    public function addImage(Image $image, ?int $position): static
    {
        $arrayPosition = array_search($image, $this->images, true);
        if (null === $position) {
            if (false !== $arrayPosition) {
                unset($this->images[$arrayPosition]);
            }
            $this->images[] = $image;
            $this->rebuildItemImages();
        } else {
            if (false !== $arrayPosition) {
                unset($this->images[$arrayPosition]);
            }
            array_splice($this->images, max($position, 0), 0, [$image]);
            $this->rebuildItemImages();
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if (false !== ($arrayPosition = array_search($image, $this->images, true))) {
            unset($this->images[$arrayPosition]);
            foreach ($this->itemImages as $key => $itemImage) {
                if ($image === $itemImage->getImage()) {
                    $this->itemImages->remove($key);
                    $this->rebuildItemImages();
                    break;
                }
            }
        }

        return $this;
    }

    #[ORM\PostLoad]
    public function onLoaded(): void
    {
        foreach ($this->itemImages as $itemImage) {
            if (null !== ($image = $itemImage->getImage())) {
                $this->images[] = $image;
            }
        }
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onChanged(): void
    {
        $this->modifiedAt = new \DateTimeImmutable();
    }

    private function rebuildItemImages(): void
    {
        $this->images = array_values($this->images);
        $itemImageKeys = $this->itemImages->getKeys();
        foreach ($this->images as $position => $image) {
            foreach ($itemImageKeys as $index => $itemImageKey) {
                /** @var ItemImage $itemImage */
                $itemImage = $this->itemImages->get($itemImageKey);
                if ($image === $itemImage->getImage()) {
                    $this->itemImages->set($itemImageKey, $itemImage->setPosition($position));
                    unset($itemImageKeys[$index]);
                    continue 2;
                }
            }

            $this->itemImages->add((new ItemImage())
                ->setItem($this)
                ->setImage($image)
                ->setPosition($position)
            );
        }
    }
}
