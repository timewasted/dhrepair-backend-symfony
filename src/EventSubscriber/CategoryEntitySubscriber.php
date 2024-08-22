<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::prePersist, entity: Category::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Category::class)]
readonly class CategoryEntitySubscriber
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function prePersist(Category $category): void
    {
        $this->setSlug($category);
    }

    public function preUpdate(Category $category): void
    {
        $this->setSlug($category);
    }

    private function setSlug(Category $category): void
    {
        if (null !== $category->getName()) {
            $category->setSlug((string) $this->slugger->slug((string) $category->getName())->lower());
        }
    }
}
