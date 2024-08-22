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
    public const string MSG_FAILURE = 'Failed to authenticate with the given credentials';

    public function __construct(readonly private UserRepository $userRepository)
    {
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($exception instanceof BadCredentialsException && null === $exception->getPrevious()) {
            $this->increaseFailedLoginCount($request);
        }

        $response = array_merge([
            'msg' => self::MSG_FAILURE,
        ], $this->getResponseBasedOnException($exception));

        return new JsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    private function increaseFailedLoginCount(Request $request): void
    {
        /** @psalm-suppress MixedAssignment */
        $requestData = json_decode($request->getContent(), true);
        if (is_array($requestData) && isset($requestData['username']) && is_string($requestData['username'])) {
            $this->userRepository->incrementFailedLoginCount($requestData['username']);
        }
    }
}
