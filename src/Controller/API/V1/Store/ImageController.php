<?php

declare(strict_types=1);

namespace App\Controller\API\V1\Store;

use App\DTO\ReadImageResponse;
use App\Entity\User;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/store', name: 'store_image_')]
class ImageController extends AbstractController
{
    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/image/{id}', name: 'read', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function read(int $id, ImageRepository $imageRepository): Response
    {
        if (null === ($image = $imageRepository->find($id))) {
            return $this->json([
                'id' => $id,
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json(new ReadImageResponse($image));
    }
}
