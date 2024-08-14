<?php

declare(strict_types=1);

namespace App\Controller\API\V1\Content;

use App\Attribute\JsonValidation;
use App\DTO\ReadPageContentResponse;
use App\DTO\UpdatePageContentRequest;
use App\Entity\User;
use App\Repository\PageContentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/content', name: 'content_')]
class ContentController extends AbstractController
{
    #[Route(path: '/{id}', name: 'read', requirements: ['id' => '[\w\-]+'], methods: ['GET'])]
    public function read(
        string $id,
        PageContentRepository $repository
    ): Response {
        if (null === ($entity = $repository->find($id))) {
            return $this->json([
                'id' => $id,
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json(new ReadPageContentResponse($entity));
    }

    #[IsGranted(User::ROLE_ADMIN)]
    #[Route(path: '/', name: 'update', methods: ['PUT'])]
    #[JsonValidation(schema: '/api/v1/content/content_update.json')]
    public function update(
        #[MapRequestPayload] UpdatePageContentRequest $updateDto,
        PageContentRepository $repository,
    ): Response {
        if (null === ($entity = $repository->update($updateDto))) {
            return $this->json([
                'id' => $updateDto->getId(),
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json(new ReadPageContentResponse($entity));
    }
}
