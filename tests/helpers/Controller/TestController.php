<?php

declare(strict_types=1);

namespace App\Tests\helpers\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class TestController extends AbstractController
{
    #[Route('/auth', name: 'auth', methods: ['GET'])]
    public function test(#[CurrentUser] ?User $user): Response
    {
        return $this->json([
            'user' => $user?->getUsernameCanonical() ?? null,
        ]);
    }
}
