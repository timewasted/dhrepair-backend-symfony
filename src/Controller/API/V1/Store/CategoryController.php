<?php

declare(strict_types=1);

namespace App\Controller\API\V1\Store;

use App\Attribute\JsonValidation;
use App\DTO\ReadCategoryResponse;
use App\DTO\UpdateCategoryRequest;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
        CategoryRepository $repository,
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

    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/category', name: 'update', methods: ['PUT'])]
    #[JsonValidation(schema: '/api/v1/store/category_update.json')]
    public function update(
        #[MapRequestPayload] UpdateCategoryRequest $updateDto,
        CategoryRepository $repository,
    ): Response {
        if (null === ($category = $repository->update($updateDto))) {
            return $this->json([
                'id' => $updateDto->getId(),
            ], Response::HTTP_NOT_FOUND);
        }
        $children = $repository->findByParent((int) $category->getId());
        $items = $repository->getItemsInCategory($category);

        return $this->json(new ReadCategoryResponse($category, $children, $items));
    }
}
