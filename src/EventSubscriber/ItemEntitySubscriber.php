<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::prePersist, entity: Item::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Item::class)]
readonly class ItemEntitySubscriber
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function prePersist(Item $item): void
    {
        $this->setSlug($item);
    }

    public function preUpdate(Item $item): void
    {
        $this->setSlug($item);
    }

    private function setSlug(Item $item): void
    {
        $slugPieces = [];
        if (null !== $item->getSku()) {
            $slugPieces[] = $item->getSku();
        }
        if (null !== $item->getName()) {
            $slugPieces[] = $item->getName();
        }
        if (!empty($slugPieces)) {
            $item->setSlug((string) $this->slugger->slug(implode('-', $slugPieces))->lower());
        }
    }
}
