<?php

declare(strict_types=1);

namespace App\Security\API\V1\JSON;

use App\Repository\UserRepository;
use App\Security\API\V1\AbstractAuthenticationFailureHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/** @psalm-suppress UnusedClass */
class AuthenticationFailureHandler extends AbstractAuthenticationFailureHandler
{
    public function __construct(readonly private UserRepository $userRepository)
    {
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($exception instanceof BadCredentialsException && null === $exception->getPrevious()) {
            $this->increaseFailedLoginCount($request);
        }

        // FIXME: Define a proper response.
        $response = array_merge([
            'path' => 'api-v1',
        ], $this->getResponseBasedOnException($exception));

        return new JsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    private function increaseFailedLoginCount(Request $request): void
    {
        $requestData = json_decode($request->getContent(), true);
        if (!is_array($requestData) || !isset($requestData['username'])) {
            return;
        }
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $requestData['username']]);
        if (null === $user) {
            return;
        }
        $this->userRepository->incrementFailedLoginCount($user);
    }
}
