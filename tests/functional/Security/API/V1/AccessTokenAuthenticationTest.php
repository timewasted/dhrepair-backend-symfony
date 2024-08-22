<?php

declare(strict_types=1);

namespace App\Tests\functional\Security\API\V1;

use App\Entity\User;
use App\Entity\UserAuthToken;
use App\Exception\Authorization\NotConfirmedException;
use App\Repository\UserRepository;
use App\Security\API\V1\AccessToken\AuthenticationFailureHandler;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;

class AccessTokenAuthenticationTest extends WebTestCase
{
    private const string AUTH_URL = '/tests/auth';

    private KernelBrowser $client;
    private UserRepository $userRepository;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = self::getContainer();

        $this->userRepository = $container->get('doctrine')->getManager()->getRepository(User::class);
    }

    public function testAuthenticationWithoutToken(): void
    {
        $this->client->jsonRequest('GET', self::AUTH_URL);

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());

        $this->assertSame([
            'user' => null,
        ], json_decode((string) $response->getContent(), true));
    }

    public function testAuthenticationSuccess(): void
    {
        $username = 'valid_user';
        $responseData = $this->doAuthRequest($username, Response::HTTP_OK);

        $this->assertSame(['user' => $username], $responseData);
    }

    public function testAuthenticationFailureInvalidToken(): void
    {
        $this->client->jsonRequest('GET', self::AUTH_URL, [], [
            'HTTP_AUTHORIZATION' => 'Bearer invalid-token',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());

        $this->assertSame([
            'msg' => AuthenticationFailureHandler::MSG_FAILURE,
            'error' => BadCredentialsException::class,
        ], json_decode((string) $response->getContent(), true));
    }

    public function testAuthenticationFailureUserUnconfirmed(): void
    {
        $username = 'unconfirmed_user';
        $responseData = $this->doAuthRequest($username, Response::HTTP_UNAUTHORIZED);

        $this->assertSame([
            'msg' => AuthenticationFailureHandler::MSG_FAILURE,
            'error' => NotConfirmedException::class,
        ], $responseData);
    }

    public function testAuthenticationFailureUserLocked(): void
    {
        $username = 'locked_user';
        $responseData = $this->doAuthRequest($username, Response::HTTP_UNAUTHORIZED);

        $this->assertSame([
            'msg' => AuthenticationFailureHandler::MSG_FAILURE,
            'error' => LockedException::class,
        ], $responseData);
    }

    public function testAuthenticationFailureUserDisabled(): void
    {
        $username = 'disabled_user';
        $responseData = $this->doAuthRequest($username, Response::HTTP_UNAUTHORIZED);

        $this->assertSame([
            'msg' => AuthenticationFailureHandler::MSG_FAILURE,
            'error' => DisabledException::class,
        ], $responseData);
    }

    public function testAuthenticationFailureUserExpired(): void
    {
        $username = 'expired_user';
        $responseData = $this->doAuthRequest($username, Response::HTTP_UNAUTHORIZED);

        $this->assertSame([
            'msg' => AuthenticationFailureHandler::MSG_FAILURE,
            'error' => AccountExpiredException::class,
        ], $responseData);
    }

    public function testAuthenticationFailureUserCredentialsExpired(): void
    {
        $username = 'credentials_expired_user';
        $responseData = $this->doAuthRequest($username, Response::HTTP_UNAUTHORIZED);

        $this->assertSame([
            'msg' => AuthenticationFailureHandler::MSG_FAILURE,
            'error' => CredentialsExpiredException::class,
        ], $responseData);
    }

    private function doAuthRequest(string $username, int $expectedStatus): array
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        /** @var UserAuthToken $authToken */
        $authToken = $user->getAuthTokens()[0];

        $this->client->jsonRequest('GET', self::AUTH_URL, [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$authToken->getAuthToken(),
        ]);

        $this->assertResponseStatusCodeSame($expectedStatus);
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());

        return (array) json_decode((string) $response->getContent(), true);
    }
}
