<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\PageContent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PageContentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $pageContent = (new PageContent())
            ->setPage('index')
            ->setTitle('Home Page')
            ->setContent('This is the content for "index"!')
        ;
        $manager->persist($pageContent);

        $pageContent = (new PageContent())
            ->setPage('about')
            ->setTitle('About Us')
            ->setContent('This is the content for "about"!')
        ;
        $manager->persist($pageContent);

        $pageContent = (new PageContent())
            ->setPage('null_title')
            ->setTitle(null)
            ->setContent('This is the content for "null_title"!')
        ;
        $manager->persist($pageContent);

        $manager->flush();
    }
}
