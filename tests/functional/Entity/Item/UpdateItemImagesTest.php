<?php

declare(strict_types=1);

namespace App\Tests\functional\Entity\Item;

use App\Entity\Image;
use App\Entity\Item;
use App\Entity\ItemImage;
use App\Repository\ImageRepository;
use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UpdateItemImagesTest extends KernelTestCase
{
    private ImageRepository $imageRepository;
    private ItemRepository $itemRepository;

    protected function setUp(): void
    {
        $container = self::getContainer();

        $entityManager = $container->get('doctrine')->getManager();
        $this->imageRepository = $entityManager->getRepository(Image::class);
        $this->itemRepository = $entityManager->getRepository(Item::class);
    }

    public function testBasicSetup(): void
    {
        $itemId = 1;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        $itemImages = $item->getItemImages();
        $this->assertCount(2, $itemImages);
        $this->validateItemImage($itemImages[0], $itemId, 1, 0);
        $this->validateItemImage($itemImages[1], $itemId, 2, 1);

        $images = $item->getImages();
        $this->assertCount(2, $images);
        $this->assertSame($itemImages[0]->getImage(), $images[0]);
        $this->assertSame($itemImages[1]->getImage(), $images[1]);
    }

    /**
     * @depends testBasicSetup
     */
    public function testExistingImageMovedFromStartToEnd(): void
    {
        $itemId = 1;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        $images = $item->getImages();
        $item->addImage($images[0], 1);
        $this->assertSame([$images[1], $images[0]], $item->getImages());

        $itemImages = $item->getItemImages();
        $this->assertCount(2, $itemImages);
        $this->validateItemImage($itemImages[0], $itemId, 1, 1);
        $this->validateItemImage($itemImages[1], $itemId, 2, 0);
    }

    /**
     * @depends testBasicSetup
     */
    public function testExistingImageMovedFromStartToEndWithNullEnd(): void
    {
        $itemId = 1;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        $images = $item->getImages();
        $item->addImage($images[0], null);
        $this->assertSame([$images[1], $images[0]], $item->getImages());

        $itemImages = $item->getItemImages();
        $this->assertCount(2, $itemImages);
        $this->validateItemImage($itemImages[0], $itemId, 1, 1);
        $this->validateItemImage($itemImages[1], $itemId, 2, 0);
    }

    /**
     * @depends testBasicSetup
     */
    public function testExistingImageMovedFromEndToStart(): void
    {
        $itemId = 1;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        $images = $item->getImages();
        $item->addImage($images[1], 0);
        $this->assertSame([$images[1], $images[0]], $item->getImages());

        $itemImages = $item->getItemImages();
        $this->assertCount(2, $itemImages);
        $this->validateItemImage($itemImages[0], $itemId, 1, 1);
        $this->validateItemImage($itemImages[1], $itemId, 2, 0);
    }

    /**
     * @depends testBasicSetup
     */
    public function testExistingImageRemovedFromStart(): void
    {
        $itemId = 1;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        $images = $item->getImages();
        $item->removeImage($images[0]);
        $this->assertSame([$images[1]], $item->getImages());

        $itemImages = $item->getItemImages();
        $this->assertCount(1, $itemImages);
        $this->validateItemImage($itemImages[1], $itemId, 2, 0);
    }

    /**
     * @depends testBasicSetup
     */
    public function testExistingImageRemovedFromEnd(): void
    {
        $itemId = 1;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        $images = $item->getImages();
        $item->removeImage($images[1]);
        $this->assertSame([$images[0]], $item->getImages());

        $itemImages = $item->getItemImages();
        $this->assertCount(1, $itemImages);
        $this->validateItemImage($itemImages[0], $itemId, 1, 0);
    }

    /**
     * @depends testBasicSetup
     */
    public function testNewImageInsertedAtStart(): void
    {
        $itemId = 1;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        /** @var Image $image */
        $image = $this->imageRepository->find(3);
        $this->assertNotContains($image, $item->getImages());

        $item->addImage($image, 0);
        $this->assertSame([
            $this->imageRepository->find(3),
            $this->imageRepository->find(1),
            $this->imageRepository->find(2),
        ], $item->getImages());

        $itemImages = $item->getItemImages();
        $this->validateItemImage($itemImages[0], $itemId, 1, 1);
        $this->validateItemImage($itemImages[1], $itemId, 2, 2);
        $this->validateItemImage($itemImages[2], $itemId, 3, 0);
    }

    /**
     * @depends testBasicSetup
     */
    public function testNewImageInsertedAtMiddle(): void
    {
        $itemId = 1;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        /** @var Image $image */
        $image = $this->imageRepository->find(3);
        $this->assertNotContains($image, $item->getImages());

        $item->addImage($image, 1);
        $this->assertSame([
            $this->imageRepository->find(1),
            $this->imageRepository->find(3),
            $this->imageRepository->find(2),
        ], $item->getImages());

        $itemImages = $item->getItemImages();
        $this->validateItemImage($itemImages[0], $itemId, 1, 0);
        $this->validateItemImage($itemImages[1], $itemId, 2, 2);
        $this->validateItemImage($itemImages[2], $itemId, 3, 1);
    }

    /**
     * @depends testBasicSetup
     */
    public function testNewImageInsertedAtEnd(): void
    {
        $itemId = 1;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        /** @var Image $image */
        $image = $this->imageRepository->find(3);
        $this->assertNotContains($image, $item->getImages());

        $item->addImage($image, 2);
        $this->assertSame([
            $this->imageRepository->find(1),
            $this->imageRepository->find(2),
            $this->imageRepository->find(3),
        ], $item->getImages());

        $itemImages = $item->getItemImages();
        $this->validateItemImage($itemImages[0], $itemId, 1, 0);
        $this->validateItemImage($itemImages[1], $itemId, 2, 1);
        $this->validateItemImage($itemImages[2], $itemId, 3, 2);
    }

    /**
     * @depends testBasicSetup
     */
    public function testNewImageInsertedAtEndWithNullEnd(): void
    {
        $itemId = 1;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        /** @var Image $image */
        $image = $this->imageRepository->find(3);
        $this->assertNotContains($image, $item->getImages());

        $item->addImage($image, null);
        $this->assertSame([
            $this->imageRepository->find(1),
            $this->imageRepository->find(2),
            $this->imageRepository->find(3),
        ], $item->getImages());

        $itemImages = $item->getItemImages();
        $this->validateItemImage($itemImages[0], $itemId, 1, 0);
        $this->validateItemImage($itemImages[1], $itemId, 2, 1);
        $this->validateItemImage($itemImages[2], $itemId, 3, 2);
    }

    /**
     * @depends testBasicSetup
     */
    public function testNewImageAttemptedToBeRemoved(): void
    {
        $itemId = 1;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        /** @var Image $image */
        $image = $this->imageRepository->find(3);
        $this->assertNotContains($image, $item->getImages());

        $item->removeImage($image);
        $this->assertSame([
            $this->imageRepository->find(1),
            $this->imageRepository->find(2),
        ], $item->getImages());

        $itemImages = $item->getItemImages();
        $this->validateItemImage($itemImages[0], $itemId, 1, 0);
        $this->validateItemImage($itemImages[1], $itemId, 2, 1);
    }

    private function validateItemImage(ItemImage $itemImage, int $itemId, int $imageId, int $position): void
    {
        $this->assertSame([
            'itemId' => $itemId,
            'imageId' => $imageId,
            'position' => $position,
        ], [
            'itemId' => $itemImage->getItem()?->getId(),
            'imageId' => $itemImage->getImage()?->getId(),
            'position' => $itemImage->getPosition(),
        ]);
    }
}
