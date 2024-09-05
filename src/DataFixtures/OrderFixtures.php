<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    private const array ORDER_USERS = [
        null,
        'admin_user',
        'valid_user',
        'temporary_user',
    ];

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $userRepository = $manager->getRepository(User::class);

        foreach (self::ORDER_USERS as $username) {
            $user = null;
            if (null !== $username) {
                $user = $userRepository->findOneBy(['usernameCanonical' => $username]);
                if (null === $user) {
                    throw new \RuntimeException(sprintf('Failed to find user "%s"', $username));
                }
            }
            for ($orderCount = 0; $orderCount < 2; ++$orderCount) {
                $order = $this->createBaseOrder($orderCount, $username);
                if (0 === $orderCount) {
                    $order->setOrderNumber(null);
                }

                $subtotal = 0;
                for ($i = 1; $i <= 3; ++$i) {
                    $cost = $i * 1000;
                    $item = (new OrderItem())
                        ->setOrderInfo($order)
                        ->setQuantity($i)
                        ->setName(sprintf('Order item %d for user %d', $i, (int) $user?->getId()))
                        ->setSku(sprintf('order-item-sku-%d', $i))
                        ->setCost($cost)
                    ;
                    $subtotal += $cost;
                    $order->addItem($item);
                }
                $order->setSubtotal($subtotal);
                $manager->persist($order);
            }
        }
        $manager->flush();
    }

    private function createBaseOrder(int $baseId, ?string $username): Order
    {
        return (new Order())
            ->setUsername($username ?? '')
            ->setOrderNumber(sprintf('%02d-090124-%d', $baseId, $baseId * 111))
            ->setReceiptId(sha1(sprintf('%02d-090124-%d', $baseId, $baseId * 111)))
            ->setBillName(bin2hex(random_bytes(16)))
            ->setBillCompany(bin2hex(random_bytes(16)))
            ->setBillAddress1(bin2hex(random_bytes(16)))
            ->setBillAddress2(bin2hex(random_bytes(16)))
            ->setBillCity(bin2hex(random_bytes(16)))
            ->setBillState(bin2hex(random_bytes(1)))
            ->setBillZipCode(bin2hex(random_bytes(8)))
            ->setBillCountry(bin2hex(random_bytes(1)))
            ->setShipName(bin2hex(random_bytes(16)))
            ->setShipCompany(bin2hex(random_bytes(16)))
            ->setShipAddress1(bin2hex(random_bytes(16)))
            ->setShipAddress2(bin2hex(random_bytes(16)))
            ->setShipCity(bin2hex(random_bytes(16)))
            ->setShipState(bin2hex(random_bytes(1)))
            ->setShipZipCode(bin2hex(random_bytes(8)))
            ->setShipCountry(bin2hex(random_bytes(1)))
            ->setPhoneNumber(bin2hex(random_bytes(16)))
            ->setEmail(null !== $username ? $username.'@example.com' : 'null@example.com')
            ->setComments(bin2hex(random_bytes(16)))
            ->setSubtotal(0)
            ->setTax(0)
            ->setShipping(0)
            ->setRefundUnusedShipping(true)
        ;
    }
}
