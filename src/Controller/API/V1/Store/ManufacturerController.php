<?php

declare(strict_types=1);

namespace App\Controller\API\V1\Store;

use App\DTO\ReadManufacturerResponse;
use App\Entity\User;
use App\Repository\ManufacturerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/store', name: 'store_manufacturer_')]
class ManufacturerController extends AbstractController
{
    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/manufacturers', name: 'list', methods: ['GET'])]
    public function list(ManufacturerRepository $repository): Response
    {
        return $this->json(new ReadManufacturerResponse($repository->findBy([], ['name' => 'ASC'])));
    }
}
