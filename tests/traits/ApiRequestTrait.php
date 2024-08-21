<?php

declare(strict_types=1);

namespace App\Tests\traits;

use App\Entity\User;
use App\Entity\UserAuthToken;

trait ApiRequestTrait
{
    protected function makeApiRequest(string $method, string $url, ?array $queryParams = null, ?\JsonSerializable $content = null, ?User $user = null): void
    {
        $parameters = (array) $content?->jsonSerialize();

        $serverParams = [];
        if (null !== $user) {
            $authTokenList = $user->getAuthTokens();
            if ($authTokenList->isEmpty()) {
                throw new \LogicException(sprintf('Expected user %s to have at least one auth token', $user->getUserIdentifier()));
            }
            /** @var UserAuthToken $authToken */
            $authToken = $authTokenList->first();
            $serverParams = [
                'HTTP_AUTHORIZATION' => 'Bearer '.$authToken->getAuthToken(),
            ];
        }

        $this->client->jsonRequest($method, $url, $parameters, $serverParams);
    }
}
