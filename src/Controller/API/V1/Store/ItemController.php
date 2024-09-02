<?php

declare(strict_types=1);

namespace App\Controller\API\V1\Store;

use App\Attribute\JsonValidation;
use App\DTO\ReadItemResponse;
use App\DTO\UpdateItemRequest;
use App\Entity\User;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/item', name: 'update', methods: ['PUT'])]
    #[JsonValidation(schema: '/api/v1/store/item_update.json')]
    public function update(
        #[MapRequestPayload(serializationContext: ['isApiRequest' => true])] UpdateItemRequest $updateDto,
        ItemRepository $itemRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        if (null === ($item = $updateDto->getItem())) {
            return $this->json([
                'id' => $updateDto->getId(),
            ], Response::HTTP_NOT_FOUND);
        }

        $item->applyUpdate($updateDto);
        $entityManager->persist($item);
        $entityManager->flush();

        $pathsToRoot = $itemRepository->getPathsToCategoryRoot($item);

        return $this->json(new ReadItemResponse($item, $pathsToRoot));
    }
}
