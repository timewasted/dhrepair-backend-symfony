<?php

declare(strict_types=1);

namespace App\Controller\API\V1\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/security', name: 'security_')]
class AuthTokenController extends AbstractController
{
    #[Route('/auth-token', name: 'auth_token', methods: ['POST'])]
    public function authToken(): Response
    {
        // See AuthenticationSuccessHandler for the logic of a successful login
        throw new \LogicException('This controller method should never be called');
    }
}
