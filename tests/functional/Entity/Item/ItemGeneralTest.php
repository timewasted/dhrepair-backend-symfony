<?php

declare(strict_types=1);

namespace App\Tests\functional\Entity\Item;

use App\Entity\Image;
use App\Entity\Item;
use App\Entity\Manufacturer;
use App\Repository\AvailabilityRepository;
use App\Repository\ImageRepository;
use App\Repository\ItemRepository;
use App\Repository\ManufacturerRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ItemGeneralTest extends KernelTestCase
{
    private ObjectManager $entityManager;
    private AvailabilityRepository $availabilityRepository;
    private ImageRepository $imageRepository;
    private ItemRepository $itemRepository;
    private ManufacturerRepository $manufacturerRepository;

    protected function setUp(): void
    {
        $container = self::getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->availabilityRepository = $container->get(AvailabilityRepository::class);
        $this->imageRepository = $this->entityManager->getRepository(Image::class);
        $this->itemRepository = $this->entityManager->getRepository(Item::class);
        $this->manufacturerRepository = $this->entityManager->getRepository(Manufacturer::class);
    }

    public function testGetImages(): void
    {
        /** @var Item $item */
        $item = $this->itemRepository->find(1);

        $this->assertSame([
            $this->imageRepository->find(1),
            $this->imageRepository->find(2),
        ], $item->getImages());
    }

    public function testSlugAfterInsert(): void
    {
        $item = $this->createItem();
        $slugPrev = $item->getSlug();
        $item->setSku('test sku 123');
        $item->setName('this is a test');
        $this->entityManager->persist($item);
        $this->entityManager->flush();

        $this->assertNotSame($slugPrev, $item->getSlug());
        $this->assertSame('test-sku-123-this-is-a-test', $item->getSlug());
    }

    public function testSlugAfterUpdate(): void
    {
        /** @var Item $item */
        $item = $this->itemRepository->find(1);
        $slugPrev = bin2hex(random_bytes(16));
        $item
            ->setSku('test sku 123')
            ->setName('this is a test')
            ->setSlug($slugPrev)
        ;
        $this->entityManager->persist($item);
        $this->entityManager->flush();

        $this->assertNotSame($slugPrev, $item->getSlug());
        $this->assertSame('test-sku-123-this-is-a-test', $item->getSlug());
    }

    public function testModifiedAtAfterInsert(): void
    {
        $item = $this->createItem();
        $this->entityManager->persist($item);
        $this->entityManager->flush();

        $currentTime = new \DateTimeImmutable();
        $this->assertEqualsWithDelta($currentTime->getTimestamp(), (int) $item->getModifiedAt()?->getTimestamp(), 2);
    }

    public function testModifiedAtAfterUpdate(): void
    {
        /** @var Item $item */
        $item = $this->itemRepository->find(1);
        $item->setName(bin2hex(random_bytes(16)));
        $this->entityManager->persist($item);
        $this->entityManager->flush();

        $currentTime = new \DateTimeImmutable();
        $this->assertEqualsWithDelta($currentTime->getTimestamp(), (int) $item->getModifiedAt()?->getTimestamp(), 2);
    }

    private function createItem(): Item
    {
        return (new Item())
            ->setName(bin2hex(random_bytes(16)))
            ->setSku(bin2hex(random_bytes(16)))
            ->setDescription(bin2hex(random_bytes(16)))
            ->setManufacturer($this->manufacturerRepository->find(1))
            ->setCost(random_int(1, 9_999_999))
            ->setQuantity(random_int(1, 9_999_999))
            ->setAvailability($this->availabilityRepository->find(1))
            ->setWeight(sprintf('%d.%02d', random_int(0, 999), random_int(0, 99)))
            ->setLength(sprintf('%d.%02d', random_int(0, 999), random_int(0, 99)))
            ->setWidth(sprintf('%d.%02d', random_int(0, 999), random_int(0, 99)))
            ->setHeight(sprintf('%d.%02d', random_int(0, 999), random_int(0, 99)))
            ->setIsProduct((bool) random_int(0, 1))
            ->setIsViewable((bool) random_int(0, 1))
            ->setIsPurchasable((bool) random_int(0, 1))
            ->setIsSpecial((bool) random_int(0, 1))
            ->setIsNew((bool) random_int(0, 1))
            ->setChargeTax((bool) random_int(0, 1))
            ->setChargeShipping((bool) random_int(0, 1))
            ->setIsFreeShipping((bool) random_int(0, 1))
            ->setFreightQuoteRequired((bool) random_int(0, 1))
        ;
    }
}
