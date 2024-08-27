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
        if (null !== $item->getName()) {
            $item->setSlug((string) $this->slugger->slug((string) $item->getName())->lower());
        }
    }
}
