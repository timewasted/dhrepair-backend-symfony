<?php

declare(strict_types=1);

namespace App\Controller\API\V1\Store;

use App\DTO\ReadAvailabilityResponse;
use App\Entity\User;
use App\Repository\AvailabilityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/store', name: 'store_availability_')]
class AvailabilityController extends AbstractController
{
    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/availabilities', name: 'list', methods: ['GET'])]
    public function list(AvailabilityRepository $repository): Response
    {
        return $this->json(new ReadAvailabilityResponse($repository->findBy([], ['availability' => 'ASC'])));
    }
}
