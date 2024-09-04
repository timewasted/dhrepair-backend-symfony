<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Availability;
use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Item;
use App\Entity\Manufacturer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ShoppingFixtures extends Fixture
{
    // NOTE: This is the first item in the first grandchild category
    public const int ITEM_ID_EVERYTHING_VIEWABLE = 5;
    // NOTE: This is the last item in the first grandchild category
    public const int ITEM_ID_NOT_VIEWABLE = 8;
    // NOTE: This is the first item in the first grandchild category of the first non-viewable root category
    public const int ITEM_ID_ANCESTOR_NOT_VIEWABLE = 149;

    private int $categoryIdSuffix = 0;
    private int $imageIdSuffix = 0;
    private int $itemIdSuffix = 0;
    private ?Availability $itemAvailability = null;
    private ?Manufacturer $itemManufacturer = null;

    public function load(ObjectManager $manager): void
    {
        /*
         * The goal here is to create the following hierarchy:
         *
         * - 6 root categories
         *   - First 3 are viewable
         *   - Last 3 are not viewable
         * - Each root category has 3 child categories
         *   - First 2 are viewable
         *   - Last 1 is not viewable
         * - Each child category has 3 grandchild categories
         *   - First 2 are viewable
         *   - Last 1 is not viewable
         * - Each non-root category has 4 items
         *   - First 2 are viewable
         *   - Last 2 is not viewable
         *   - First and third have 2 images
         *   - Second and fourth have no images
         */

        $this->createItemAvailabilities($manager);
        $this->createItemManufacturers($manager);
        $manager->flush();

        $this->categoryIdSuffix = 0;
        $this->imageIdSuffix = 0;
        $this->itemIdSuffix = 0;
        for ($i = 0; $i < 6; ++$i) {
            $this->createCategoryLevel($manager, 0, 3, null, $i < 3);
        }
    }

    private function createBaseCategory(?Category $parent, string $labelPrefix, int $idSuffix, bool $isViewable): Category
    {
        return (new Category())
            ->setParent($parent)
            ->setName(sprintf('%s category %d', $labelPrefix, $idSuffix))
            ->setSlug(sprintf('%s-category-%d', str_replace(' ', '-', strtolower($labelPrefix)), $idSuffix))
            ->setDescription(sprintf('Description for %s category %d', strtolower($labelPrefix), $idSuffix))
            ->setIsViewable($isViewable)
        ;
    }

    private function createBaseImage(int $idSuffix): Image
    {
        return (new Image())
            ->setImage(sprintf('image-%d.jpg', $idSuffix))
            ->setImageHash(hash('sha256', sprintf('image-%d.jpg', $idSuffix)))
            ->setTitle(sprintf('Image %d', $idSuffix))
            ->setWidth(random_int(1000, 2000))
            ->setHeight(random_int(1000, 2000))
            ->setThumbWidth(random_int(500, 1000))
            ->setThumbHeight(random_int(500, 1000))
        ;
    }

    private function createBaseItem(Category $parent, int $idSuffix, bool $isViewable): Item
    {
        $item = (new Item())
            ->setName(sprintf('Item %d', $idSuffix))
            ->setSlug(sprintf('item-%d', $idSuffix))
            ->setSku(sprintf('sku-%d', $idSuffix))
            ->setDescription(sprintf('Description for item %d', $idSuffix))
            ->setManufacturer($this->itemManufacturer)
            ->setCost($idSuffix)
            ->setAvailability($this->itemAvailability)
            ->setIsViewable($isViewable)
        ;
        $parent->addItem($item);

        return $item;
    }

    private function createCategoryLevel(ObjectManager $manager, int $currentLevel, int $maxLevel, ?Category $parent, bool $isViewable): void
    {
        switch ($currentLevel) {
            case 0:
                $labelPrefix = 'Root';
                break;
            case 1:
                $labelPrefix = 'Child';
                break;
            case 2:
                $labelPrefix = 'Grandchild';
                break;
            default:
                $labelPrefix = str_repeat('great ', $currentLevel - 2).'grandchild';
                $labelPrefix[0] = 'G';
                break;
        }

        $category = $this->createBaseCategory($parent, $labelPrefix, ++$this->categoryIdSuffix, $isViewable);
        $manager->persist($category);
        $manager->flush();

        if (0 !== $currentLevel) {
            for ($i = 0; $i < 4; ++$i) {
                $item = $this->createBaseItem($category, ++$this->itemIdSuffix, $i < 2);
                if (0 === $i || 2 === $i) {
                    $item->addImage($this->createBaseImage(++$this->imageIdSuffix), 1);
                    $item->addImage($this->createBaseImage(++$this->imageIdSuffix), 2);
                }
                $manager->persist($item);
            }
            $manager->flush();
        }
        if (++$currentLevel < $maxLevel) {
            for ($i = 0; $i < 3; ++$i) {
                $this->createCategoryLevel($manager, $currentLevel, $maxLevel, $category, 2 !== $i);
            }
        }
    }

    private function createItemAvailabilities(ObjectManager $manager): void
    {
        $values = [
            'Out of stock',
            'In stock',
            'Ships within 1-3 business days',
        ];
        foreach ($values as $value) {
            $availability = (new Availability())->setAvailability($value);
            $manager->persist($availability);
            if (!isset($this->itemAvailability)) {
                $this->itemAvailability = $availability;
            }
        }
        $manager->flush();
    }

    private function createItemManufacturers(ObjectManager $manager): void
    {
        $values = [
            'N/A',
            'Manufacturer 1',
            'Manufacturer 2',
        ];
        foreach ($values as $value) {
            $manufacturer = (new Manufacturer())->setName($value);
            $manager->persist($manufacturer);
            if (!isset($this->itemManufacturer)) {
                $this->itemManufacturer = $manufacturer;
            }
        }
        $manager->flush();
    }
}
