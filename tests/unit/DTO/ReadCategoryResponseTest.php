<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\ReadCategoryResponse;
use App\Entity\Availability;
use App\Entity\Category;
use App\Entity\Item;
use App\Entity\Manufacturer;
use PHPUnit\Framework\TestCase;

class ReadCategoryResponseTest extends TestCase
{
    public function testJsonSerializeNoCategoryNoChildrenNoItems(): void
    {
        $dto = new ReadCategoryResponse(null, [], []);

        $this->assertSame([
            'category' => null,
            'parent' => null,
            'children' => [
                'categories' => [],
                'items' => [],
            ],
        ], $dto->jsonSerialize());
    }

    public function testJsonSerializeNoParentNoChildrenNoItems(): void
    {
        $category = $this->createCategory();
        $dto = new ReadCategoryResponse($category, [], []);

        $this->assertSame([
            'category' => $this->relevantCategoryFields($category),
            'parent' => null,
            'children' => [
                'categories' => [],
                'items' => [],
            ],
        ], $dto->jsonSerialize());
    }

    public function testJsonSerializeNoChildrenNoItems(): void
    {
        $parent = $this->createCategory();
        $category = $this->createCategory($parent);
        $dto = new ReadCategoryResponse($category, [], []);

        $this->assertSame([
            'category' => $this->relevantCategoryFields($category),
            'parent' => $this->relevantCategoryFields($parent),
            'children' => [
                'categories' => [],
                'items' => [],
            ],
        ], $dto->jsonSerialize());
    }

    public function testJsonSerializeNoItems(): void
    {
        $category = $this->createCategory();
        $children = [
            $this->createCategory(),
            $this->createCategory(),
            $this->createCategory(),
        ];
        $dto = new ReadCategoryResponse($category, $children, []);

        $this->assertSame([
            'category' => $this->relevantCategoryFields($category),
            'parent' => null,
            'children' => [
                'categories' => [
                    $this->relevantCategoryFields($children[0]),
                    $this->relevantCategoryFields($children[1]),
                    $this->relevantCategoryFields($children[2]),
                ],
                'items' => [],
            ],
        ], $dto->jsonSerialize());
    }

    public function testJsonSerialize(): void
    {
        $category = $this->createCategory();
        $children = [
            $this->createCategory(),
            $this->createCategory(),
            $this->createCategory(),
        ];
        $items = [
            $this->createItem(),
            $this->createItem(),
            $this->createItem(),
        ];
        $dto = new ReadCategoryResponse($category, $children, $items);

        $this->assertSame([
            'category' => $this->relevantCategoryFields($category),
            'parent' => null,
            'children' => [
                'categories' => [
                    $this->relevantCategoryFields($children[0]),
                    $this->relevantCategoryFields($children[1]),
                    $this->relevantCategoryFields($children[2]),
                ],
                'items' => [
                    $this->relevantItemFields($items[0]),
                    $this->relevantItemFields($items[1]),
                    $this->relevantItemFields($items[2]),
                ],
            ],
        ], $dto->jsonSerialize());
    }

    private function createCategory(?Category $parent = null): Category
    {
        return (new Category())
            ->setParent($parent)
            ->setName(bin2hex(random_bytes(16)))
            ->setSlug(bin2hex(random_bytes(16)))
            ->setDescription(bin2hex(random_bytes(16)))
            ->setIsViewable(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setModifiedAt(new \DateTimeImmutable())
        ;
    }

    private function createItem(): Item
    {
        return (new Item())
            ->setName(bin2hex(random_bytes(16)))
            ->setSlug(bin2hex(random_bytes(16)))
            ->setSku(bin2hex(random_bytes(16)))
            ->setDescription(bin2hex(random_bytes(16)))
            ->setManufacturer((new Manufacturer())->setName(bin2hex(random_bytes(16))))
            ->setCost(random_int(1, PHP_INT_MAX))
            ->setQuantity(random_int(1, PHP_INT_MAX))
            ->setAvailability((new Availability())->setAvailability(bin2hex(random_bytes(16))))
            ->setWeight(bin2hex(random_bytes(16)))
            ->setLength(bin2hex(random_bytes(16)))
            ->setWidth(bin2hex(random_bytes(16)))
            ->setHeight(bin2hex(random_bytes(16)))
            ->setIsProduct(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setIsViewable(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setIsPurchasable(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setIsSpecial(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setIsNew(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setChargeTax(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setChargeShipping(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setIsFreeShipping(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setFreightQuoteRequired(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setModifiedAt(new \DateTimeImmutable())
        ;
    }

    private function relevantCategoryFields(Category $category): array
    {
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
            'description' => $category->getDescription(),
            'isViewable' => $category->isViewable(),
            'modifiedAt' => $category->getModifiedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }

    private function relevantItemFields(Item $item): array
    {
        return [
            'id' => $item->getId(),
            'name' => $item->getName(),
            'slug' => $item->getSlug(),
            'sku' => $item->getSku(),
            'description' => $item->getDescription(),
            'manufacturer' => $item->getManufacturer()?->getName(),
            'cost' => $item->getCost(),
            'quantity' => $item->getQuantity(),
            'availability' => $item->getAvailability()?->getAvailability(),
            'weight' => $item->getWeight(),
            'length' => $item->getLength(),
            'width' => $item->getWidth(),
            'height' => $item->getHeight(),
            'isProduct' => $item->isProduct(),
            'isViewable' => $item->isViewable(),
            'isPurchasable' => $item->isPurchasable(),
            'isSpecial' => $item->isSpecial(),
            'isNew' => $item->isNew(),
            'chargeTax' => $item->isChargeTax(),
            'chargeShipping' => $item->isChargeShipping(),
            'isFreeShipping' => $item->isFreeShipping(),
            'freightQuoteRequired' => $item->isFreightQuoteRequired(),
            'modifiedAt' => $item->getModifiedAt()?->format(\DateTimeInterface::ATOM),
            'images' => [],
        ];
    }
}
