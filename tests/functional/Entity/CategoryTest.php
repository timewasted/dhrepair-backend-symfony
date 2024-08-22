<?php

declare(strict_types=1);

namespace App\Tests\functional\Entity;

use App\Entity\Category;
use App\Entity\CategoryClosure;
use App\Repository\CategoryClosureRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryTest extends KernelTestCase
{
    private ObjectManager $entityManager;
    private CategoryRepository $categoryRepository;
    private CategoryClosureRepository $categoryClosureRepository;

    protected function setUp(): void
    {
        $container = self::getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->categoryRepository = $this->entityManager->getRepository(Category::class);
        $this->categoryClosureRepository = $this->entityManager->getRepository(CategoryClosure::class);
    }

    public function testEntityInserted(): void
    {
        $parent = $this->createCategory(null);
        $this->entityManager->persist($parent);
        $this->entityManager->flush();

        // Ensure that there is only a single entity (itself) with this category as a parent
        $closureByParent = $this->categoryClosureRepository->findBy(['parent' => $parent->getId()]);
        $this->assertCount(1, $closureByParent);
        $this->validateClosure($closureByParent[0], $parent->getId(), $parent->getId(), 0);

        // Ensure that there is only a single entity (itself) with this category as a child
        $closureByChild = $this->categoryClosureRepository->findBy(['child' => $parent->getId()]);
        $this->assertCount(1, $closureByChild);
        $this->validateClosure($closureByChild[0], $parent->getId(), $parent->getId(), 0);

        $category = $this->createCategory($parent);
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        // Ensure that there is only a single entity (itself) with this category as a parent
        $closureByParent = $this->categoryClosureRepository->findBy(['parent' => $category->getId()]);
        $this->assertCount(1, $closureByParent);
        $this->validateClosure($closureByParent[0], $category->getId(), $category->getId(), 0);

        // Ensure that there are only two entities (itself, and its parent) with this category as a child
        $closureByChild = $this->categoryClosureRepository->findBy(['child' => $category->getId()], ['depth' => 'ASC']);
        $this->assertCount(2, $closureByChild);
        $this->validateClosure($closureByChild[0], $category->getId(), $category->getId(), 0);
        $this->validateClosure($closureByChild[1], $parent->getId(), $category->getId(), 1);
    }

    public function testEntityUpdatedParentNotChanged(): void
    {
        $categoryId = 3;
        /** @var Category $category */
        $category = $this->categoryRepository->find($categoryId);

        $doByParentAssertions = function (int $categoryId): void {
            $closures = $this->categoryClosureRepository->findBy(['parent' => $categoryId]);
            $this->assertCount(1, $closures);
            $this->validateClosure($closures[0], $categoryId, $categoryId, 0);
        };
        $doByChildAssertions = function (int $categoryId): void {
            $closures = $this->categoryClosureRepository->findBy(['child' => $categoryId], ['depth' => 'ASC']);
            $this->assertCount(3, $closures);
            $this->validateClosure($closures[0], $categoryId, $categoryId, 0);
            $this->validateClosure($closures[1], 2, $categoryId, 1);
            $this->validateClosure($closures[2], 1, $categoryId, 2);
        };

        $doByParentAssertions($categoryId);
        $doByChildAssertions($categoryId);

        $category->setName(bin2hex(random_bytes(16)));
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $doByParentAssertions($categoryId);
        $doByChildAssertions($categoryId);
    }

    public function testEntityUpdateParentChangedNullParent(): void
    {
        $categoryId = 3;
        $category = $this->categoryRepository->find($categoryId);
        $this->assertNotNull($category);
        $this->assertNotNull($category->getParent());

        $closureByParent = $this->categoryClosureRepository->findBy(['parent' => $categoryId]);
        $this->assertCount(1, $closureByParent);
        $this->validateClosure($closureByParent[0], $categoryId, $categoryId, 0);

        $closureByChild = $this->categoryClosureRepository->findBy(['child' => $categoryId], ['depth' => 'ASC']);
        $this->assertCount(3, $closureByChild);
        $this->validateClosure($closureByChild[0], $categoryId, $categoryId, 0);
        $this->validateClosure($closureByChild[1], 2, $categoryId, 1);
        $this->validateClosure($closureByChild[2], 1, $categoryId, 2);

        $category->setParent(null);
        $this->entityManager->persist($category);
        $this->entityManager->flush();
        $this->assertNull($category->getParent());

        $closureByParent = $this->categoryClosureRepository->findBy(['parent' => $categoryId]);
        $this->assertCount(1, $closureByParent);
        $this->validateClosure($closureByParent[0], $categoryId, $categoryId, 0);

        $closureByChild = $this->categoryClosureRepository->findBy(['child' => $categoryId], ['depth' => 'ASC']);
        $this->assertCount(1, $closureByChild);
        $this->validateClosure($closureByChild[0], $categoryId, $categoryId, 0);
    }

    public function testEntityUpdatedParentChanged(): void
    {
        $categoryId = 3;
        $parentId = 1;
        $category = $this->categoryRepository->find($categoryId);
        $this->assertNotNull($category);
        $parentOrig = $category->getParent();
        $this->assertNotNull($parentOrig);
        $this->assertNotSame($parentId, $parentOrig->getId());
        $parentNew = $this->categoryRepository->find($parentId);
        $this->assertNotNull($parentNew);

        $closureByParent = $this->categoryClosureRepository->findBy(['parent' => $categoryId]);
        $this->assertCount(1, $closureByParent);
        $this->validateClosure($closureByParent[0], $categoryId, $categoryId, 0);

        $closureByChild = $this->categoryClosureRepository->findBy(['child' => $categoryId], ['depth' => 'ASC']);
        $this->assertCount(3, $closureByChild);
        $this->validateClosure($closureByChild[0], $categoryId, $categoryId, 0);
        $this->validateClosure($closureByChild[1], 2, $categoryId, 1);
        $this->validateClosure($closureByChild[2], 1, $categoryId, 2);

        $category->setParent($parentNew);
        $this->entityManager->persist($category);
        $this->entityManager->flush();
        $this->assertSame($parentNew, $category->getParent());

        $closureByParent = $this->categoryClosureRepository->findBy(['parent' => $categoryId]);
        $this->assertCount(1, $closureByParent);
        $this->validateClosure($closureByParent[0], $categoryId, $categoryId, 0);

        $closureByChild = $this->categoryClosureRepository->findBy(['child' => $categoryId], ['depth' => 'ASC']);
        $this->assertCount(2, $closureByChild);
        $this->validateClosure($closureByChild[0], $categoryId, $categoryId, 0);
        $this->validateClosure($closureByChild[1], 1, $categoryId, 1);
    }

    private function createCategory(?Category $parent): Category
    {
        return (new Category())
            ->setParent($parent)
            ->setName(bin2hex(random_bytes(16)))
            ->setDescription(bin2hex(random_bytes(16)))
            ->setIsViewable((bool) random_int(0, 1))
        ;
    }

    private function validateClosure(CategoryClosure $closure, ?int $parent, ?int $child, int $depth): void
    {
        $this->assertSame(
            [$parent ?? 0, $child ?? 0, $depth],
            [$closure->getParent(), $closure->getChild(), $closure->getDepth()]
        );
    }
}
