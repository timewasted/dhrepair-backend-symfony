<?php

declare(strict_types=1);

namespace App\Controller\API\V1\Store;

use App\DTO\ReadItemResponse;
use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/store', name: 'store_item_')]
class ItemController extends AbstractController
{
    #[Route('/item/{id}', name: 'read', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function read(
        int $id,
        ItemRepository $itemRepository,
    ): Response {
        $item = $itemRepository->find($id);
        if (null === $item || !$itemRepository->isViewable($item)) {
            return $this->json([
                'id' => $id,
            ], Response::HTTP_NOT_FOUND);
        }
        $pathsToRoot = $itemRepository->getPathsToCategoryRoot($item);

        return $this->json(new ReadItemResponse($item, $pathsToRoot));
    }
}
