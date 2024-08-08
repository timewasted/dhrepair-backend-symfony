<?php

declare(strict_types=1);

namespace App\Tests\functional\Security\API\V1;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Entity\UserAuthToken;
use App\Exception\Authorization\NotConfirmedException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class JsonAuthenticationTest extends WebTestCase
{
    private const string AUTH_URL = '/api/v1/security/auth-token';

    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = self::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $this->entityManager = $em;
    }

    public function testAuthenticationSuccess(): void
    {
        $username = 'valid_user';
        $responseData = $this->doAuthRequest($username, UserFixtures::DEFAULT_PASSWORD, Response::HTTP_OK);

        $this->assertArrayHasKey('user', $responseData);
        $this->assertSame($username, $responseData['user']);

        $this->assertArrayHasKey('token', $responseData);
        $userAuthToken = $this->entityManager->getRepository(UserAuthToken::class)->findOneBy(['authToken' => $responseData['token']]);
        $this->assertNotNull($userAuthToken);
        /** @var User $user */
        $user = $userAuthToken->getUser();
        $this->assertSame($username, $user->getUsernameCanonical());

        $this->assertSame([
            'user' => $user->getUsernameCanonical(),
            'token' => $responseData['token'],
        ], $responseData);

        /** @var \DateTimeInterface $lastLogin */
        $lastLogin = $user->getLastLogin();
        $this->assertEqualsWithDelta((new \DateTimeImmutable())->getTimestamp(), $lastLogin->getTimestamp(), 2);
    }

    public function testAuthenticationFailureInvalidUsername(): void
    {
        $username = 'invalid';
        $responseData = $this->doAuthRequest($username, UserFixtures::DEFAULT_PASSWORD, Response::HTTP_UNAUTHORIZED);

        $this->assertSame([
            'path' => 'api-v1',
            'error' => UserNotFoundException::class,
        ], $responseData);
    }

    public function testAuthenticationFailureInvalidPassword(): void
    {
        $username = 'valid_user';
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['usernameCanonical' => $username]);
        $failedLoginAttempts = $user->getFailedLoginAttempts();
        $responseData = $this->doAuthRequest($username, 'invalid', Response::HTTP_UNAUTHORIZED);

        $this->assertSame([
            'path' => 'api-v1',
            'error' => BadCredentialsException::class,
        ], $responseData);

        $this->entityManager->refresh($user);
        $this->assertSame($failedLoginAttempts + 1, $user->getFailedLoginAttempts());
    }

    /**
     * @depends testAuthenticationSuccess
     * @depends testAuthenticationFailureInvalidPassword
     */
    public function testAuthenticationSuccessAfterInvalidPasswordResetsFailedLoginAttempts(): void
    {
        $username = 'valid_user';
        $this->doAuthRequest($username, 'invalid', Response::HTTP_UNAUTHORIZED);
        $this->doAuthRequest($username, UserFixtures::DEFAULT_PASSWORD, Response::HTTP_OK);

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['usernameCanonical' => $username]);
        $this->assertSame(0, $user->getFailedLoginAttempts());
    }

    public function testAuthenticationFailureUserUnconfirmed(): void
    {
        $username = 'unconfirmed_user';
        $responseData = $this->doAuthRequest($username, UserFixtures::DEFAULT_PASSWORD, Response::HTTP_UNAUTHORIZED);

        $this->assertSame([
            'path' => 'api-v1',
            'error' => NotConfirmedException::class,
        ], $responseData);
    }

    public function testAuthenticationFailureUserLocked(): void
    {
        $username = 'locked_user';
        $responseData = $this->doAuthRequest($username, UserFixtures::DEFAULT_PASSWORD, Response::HTTP_UNAUTHORIZED);

        $this->assertSame([
            'path' => 'api-v1',
            'error' => LockedException::class,
        ], $responseData);
    }

    public function testAuthenticationFailureUserDisabled(): void
    {
        $username = 'disabled_user';
        $responseData = $this->doAuthRequest($username, UserFixtures::DEFAULT_PASSWORD, Response::HTTP_UNAUTHORIZED);

        $this->assertSame([
            'path' => 'api-v1',
            'error' => DisabledException::class,
        ], $responseData);
    }

    public function testAuthenticationFailureUserExpired(): void
    {
        $username = 'expired_user';
        $responseData = $this->doAuthRequest($username, UserFixtures::DEFAULT_PASSWORD, Response::HTTP_UNAUTHORIZED);

        $this->assertSame([
            'path' => 'api-v1',
            'error' => AccountExpiredException::class,
        ], $responseData);
    }

    public function testAuthenticationFailureUserCredentialsExpired(): void
    {
        $username = 'credentials_expired_user';
        $responseData = $this->doAuthRequest($username, UserFixtures::DEFAULT_PASSWORD, Response::HTTP_UNAUTHORIZED);

        $this->assertSame([
            'path' => 'api-v1',
            'error' => CredentialsExpiredException::class,
        ], $responseData);
    }

    private function doAuthRequest(string $username, string $password, int $expectedStatus): array
    {
        $this->client->jsonRequest('POST', self::AUTH_URL, [
            'username' => $username,
            'password' => $password,
        ]);

        $this->assertResponseStatusCodeSame($expectedStatus);
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());

        return (array) json_decode((string) $response->getContent(), true);
    }
}
