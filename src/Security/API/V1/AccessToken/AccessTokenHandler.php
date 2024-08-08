<?php

declare(strict_types=1);

namespace App\Security\API\V1\AccessToken;

use App\Repository\UserAuthTokenRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

/** @psalm-suppress UnusedClass */
readonly class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(private UserAuthTokenRepository $repository)
    {
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $accessToken = $this->repository->findOneBy(['authToken' => $accessToken]);
        if (null === $accessToken) {
            throw new BadCredentialsException('Invalid authorization token.');
        }
        if (null === ($user = $accessToken->getUser())) {
            throw new UserNotFoundException('No user associated with this authorization token.');
        }

        return new UserBadge($user->getUserIdentifier());
    }
}
