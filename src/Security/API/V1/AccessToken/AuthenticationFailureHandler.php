<?php

declare(strict_types=1);

namespace App\Security\API\V1\AccessToken;

use App\Security\API\V1\AbstractAuthenticationFailureHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/** @psalm-suppress UnusedClass */
class AuthenticationFailureHandler extends AbstractAuthenticationFailureHandler
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        // FIXME: Define a proper response.
        $response = array_merge([
            'path' => 'access-token',
        ], $this->getResponseBasedOnException($exception));

        return new JsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }
}
