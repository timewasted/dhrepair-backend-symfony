<?php

declare(strict_types=1);

namespace App\Tests\traits;

use App\Entity\User;
use App\Entity\UserAuthToken;
use Symfony\Component\HttpFoundation\Response;

trait ApiRequestTrait
{
    protected function getJsonResponseData(int $expectedStatusCode = Response::HTTP_OK): array
    {
        $this->assertResponseStatusCodeSame($expectedStatusCode);
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());

        return (array) json_decode((string) $response->getContent(), true);
    }

    protected function makeApiRequest(string $method, string $url, ?array $queryParams = null, mixed $content = null, ?User $user = null): void
    {
        if ($content instanceof \JsonSerializable) {
            $parameters = (array) $content->jsonSerialize();
        } elseif (null === $content) {
            $parameters = [];
        } elseif (is_array($content)) {
            $parameters = $content;
        } else {
            throw new \InvalidArgumentException('Content should be null, an array, or an object that implements JsonSerializable');
        }

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
