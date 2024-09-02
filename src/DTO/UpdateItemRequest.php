<?php

declare(strict_types=1);

namespace App\DTO;

use App\Attribute\DenormalizeEntity;
use App\Entity\Availability;
use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Item;
use App\Entity\Manufacturer;
use Symfony\Component\Serializer\Attribute\Context;

class UpdateItemRequest implements \JsonSerializable
{
    #[Context(denormalizationContext: [Item::class => ['denormalized' => true]])]
    #[DenormalizeEntity(class: Item::class, dataSource: 'id', nullable: true)]
    private ?Item $item;

    #[Context(denormalizationContext: [Manufacturer::class => ['denormalized' => true]])]
    #[DenormalizeEntity(class: Manufacturer::class, dataSource: 'manufacturerId')]
    private Manufacturer $manufacturer;

    #[Context(denormalizationContext: [Availability::class => ['denormalized' => true]])]
    #[DenormalizeEntity(class: Availability::class, dataSource: 'availabilityId')]
    private Availability $availability;

    /**
     * @var Category[]
     */
    #[DenormalizeEntity(class: Category::class, dataSource: 'categoryIds', isCollection: true)]
    private array $categories;

    /**
     * @var Image[]
     */
    #[DenormalizeEntity(class: Image::class, dataSource: 'imageIds', isCollection: true)]
    private array $images;

    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $sku,
        private readonly string $description,
        private readonly int $manufacturerId,
        private readonly int $cost,
        private readonly int $quantity,
        private readonly int $availabilityId,
        private readonly string $weight,
        private readonly string $length,
        private readonly string $width,
        private readonly string $height,
        private readonly bool $isProduct,
        private readonly bool $isViewable,
        private readonly bool $isPurchasable,
        private readonly bool $isSpecial,
        private readonly bool $isNew,
        private readonly bool $chargeTax,
        private readonly bool $chargeShipping,
        private readonly bool $isFreeShipping,
        private readonly bool $freightQuoteRequired,
        private readonly array $categoryIds,
        private readonly array $imageIds,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getManufacturerId(): int
    {
        return $this->manufacturerId;
    }

    public function getCost(): int
    {
        return $this->cost;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getAvailabilityId(): int
    {
        return $this->availabilityId;
    }

    public function getWeight(): string
    {
        return $this->weight;
    }

    public function getLength(): string
    {
        return $this->length;
    }

    public function getWidth(): string
    {
        return $this->width;
    }

    public function getHeight(): string
    {
        return $this->height;
    }

    public function isProduct(): bool
    {
        return $this->isProduct;
    }

    public function isViewable(): bool
    {
        return $this->isViewable;
    }

    public function isPurchasable(): bool
    {
        return $this->isPurchasable;
    }

    public function isSpecial(): bool
    {
        return $this->isSpecial;
    }

    public function isNew(): bool
    {
        return $this->isNew;
    }

    public function isChargeTax(): bool
    {
        return $this->chargeTax;
    }

    public function isChargeShipping(): bool
    {
        return $this->chargeShipping;
    }

    public function isFreeShipping(): bool
    {
        return $this->isFreeShipping;
    }

    public function isFreightQuoteRequired(): bool
    {
        return $this->freightQuoteRequired;
    }

    public function getCategoryIds(): array
    {
        return $this->categoryIds;
    }

    public function getImageIds(): array
    {
        return $this->imageIds;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(Item $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function getManufacturer(): Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(Manufacturer $manufacturer): static
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getAvailability(): Availability
    {
        return $this->availability;
    }

    public function setAvailability(Availability $availability): static
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * @return Category[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param Category[] $categories
     */
    public function setCategories(array $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param Image[] $images
     */
    public function setImages(array $images): static
    {
        $this->images = $images;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'sku' => $this->getSku(),
            'description' => $this->getDescription(),
            'manufacturerId' => $this->getManufacturerId(),
            'cost' => $this->getCost(),
            'quantity' => $this->getQuantity(),
            'availabilityId' => $this->getAvailabilityId(),
            'weight' => $this->getWeight(),
            'length' => $this->getLength(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
            'isProduct' => $this->isProduct(),
            'isViewable' => $this->isViewable(),
            'isPurchasable' => $this->isPurchasable(),
            'isSpecial' => $this->isSpecial(),
            'isNew' => $this->isNew(),
            'chargeTax' => $this->isChargeTax(),
            'chargeShipping' => $this->isChargeShipping(),
            'isFreeShipping' => $this->isFreeShipping(),
            'freightQuoteRequired' => $this->isFreightQuoteRequired(),
            'categoryIds' => $this->getCategoryIds(),
            'imageIds' => $this->getImageIds(),
        ];
    }
}
