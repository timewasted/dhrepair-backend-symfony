<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Item;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CartFixtures extends Fixture implements DependentFixtureInterface
{
    private const array CART_USERS = [
        'admin_user',
        'valid_user',
        'temporary_user',
    ];

    public function getDependencies(): array
    {
        return [
            ShoppingFixtures::class,
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $itemRepository = $manager->getRepository(Item::class);
        $userRepository = $manager->getRepository(User::class);

        $cartDetails = [
            ShoppingFixtures::ITEM_ID_EVERYTHING_VIEWABLE => 3,
            ShoppingFixtures::ITEM_ID_NOT_VIEWABLE => 2,
            ShoppingFixtures::ITEM_ID_ANCESTOR_NOT_VIEWABLE => 1,
        ];

        foreach (self::CART_USERS as $username) {
            $user = $userRepository->findOneBy(['usernameCanonical' => $username]);
            if (null === $user) {
                throw new \RuntimeException(sprintf('Failed to find user "%s"', $username));
            }
            foreach ($cartDetails as $itemId => $quantity) {
                $item = $itemRepository->find($itemId);
                if (null === $item) {
                    throw new \RuntimeException(sprintf('Failed to find item %d', $itemId));
                }
                $user->addCartItem($item, $quantity);
            }
            $manager->persist($user);
        }
        $manager->flush();
    }
}
