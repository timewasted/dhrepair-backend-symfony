<?php

declare(strict_types=1);

namespace App\Security\API\V1;

use App\Exception\Authorization\NotConfirmedException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

abstract class AbstractAuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    protected function getResponseBasedOnException(AuthenticationException $exception): array
    {
        if ($exception instanceof BadCredentialsException && null !== $exception->getPrevious()) {
            $exception = $exception->getPrevious();
        }

        // FIXME: Define proper responses for these cases.
        return match (true) {
            $exception instanceof AccountExpiredException => ['error' => AccountExpiredException::class],
            $exception instanceof CredentialsExpiredException => ['error' => CredentialsExpiredException::class],
            $exception instanceof DisabledException => ['error' => DisabledException::class],
            $exception instanceof LockedException => ['error' => LockedException::class],
            $exception instanceof NotConfirmedException => ['error' => NotConfirmedException::class],
            $exception instanceof UnsupportedUserException => ['error' => UnsupportedUserException::class],
            $exception instanceof UserNotFoundException => ['error' => UserNotFoundException::class],
            default => ['error' => $exception::class],
        };
    }
}
