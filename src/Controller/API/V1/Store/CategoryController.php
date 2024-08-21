<?php

declare(strict_types=1);

namespace App\Controller\API\V1\Store;

use App\DTO\ReadCategoryResponse;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/store', name: 'store_category_')]
class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'list', methods: ['GET'])]
    public function list(CategoryRepository $repository): Response
    {
        $categories = $repository->findByParent(null);

        return $this->json(new ReadCategoryResponse(null, $categories, []));
    }

    #[Route('/category/{id}', name: 'read', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function read(
        int $id,
        CategoryRepository $repository
    ): Response {
        $category = $repository->find($id);
        if (null === $category || !$repository->isViewable($category)) {
            return $this->json([
                'id' => $id,
            ], Response::HTTP_NOT_FOUND);
        }
        $children = $repository->findByParent((int) $category->getId());
        $items = $repository->getItemsInCategory($category);

        return $this->json(new ReadCategoryResponse($category, $children, $items));
    }
}
