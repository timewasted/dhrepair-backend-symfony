<?php

declare(strict_types=1);

namespace App\Tests\helpers\DTO;

use App\Attribute\DenormalizeEntity;
use App\Entity\Image;
use App\Entity\Item;
use App\Entity\Manufacturer;
use Symfony\Component\Serializer\Attribute\Context;

readonly class TestApiRequestValid
{
    public function __construct(
        private int $itemId,
        /**
         * @var int[]
         */
        private array $imageIds,
        #[Context(denormalizationContext: [Item::class => ['denormalized' => true]])]
        #[DenormalizeEntity(class: Item::class, dataSource: 'itemId')]
        private Item $item,
        /**
         * @var Image[]
         */
        #[DenormalizeEntity(class: Image::class, dataSource: 'imageIds', isCollection: true)]
        private array $images,
        #[DenormalizeEntity(class: Manufacturer::class)]
        private Manufacturer $manufacturer,
    ) {
    }

    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function getImageIds(): array
    {
        return $this->imageIds;
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function getManufacturer(): Manufacturer
    {
        return $this->manufacturer;
    }
}
