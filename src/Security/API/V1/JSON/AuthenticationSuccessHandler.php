<?php

declare(strict_types=1);

namespace App\Security\API\V1\JSON;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/** @psalm-suppress UnusedClass */
readonly class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            throw new UnsupportedUserException();
        }

        $userAuthToken = $user
            ->setLastLogin(new \DateTimeImmutable())
            ->setFailedLoginAttempts(0)
            ->addAuthToken()
        ;
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // FIXME: Define a proper response.
        return new JsonResponse([
            'user' => $user->getUserIdentifier(),
            'token' => $userAuthToken->getAuthToken(),
        ]);
    }
}
