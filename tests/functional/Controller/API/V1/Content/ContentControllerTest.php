<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Content;

use App\DTO\UpdatePageContentRequest;
use App\Entity\PageContent;
use App\Entity\User;
use App\Repository\PageContentRepository;
use App\Repository\UserRepository;
use App\Tests\traits\ApiRequestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ContentControllerTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string CONTENT_URL = '/api/v1/content/';

    private KernelBrowser $client;
    private PageContentRepository $contentRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        $container = self::getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        $this->contentRepository = $entityManager->getRepository(PageContent::class);
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    /**
     * @return list<array{string}>
     */
    public static function providerUsername(): array
    {
        return [
            ['temporary_user'],
            ['valid_user'],
            ['admin_user'],
            ['super_admin_user'],
        ];
    }

    /**
     * @return list<array{string, bool}>
     */
    public static function providerUsernameUpdateStatus(): array
    {
        return [
            ['temporary_user', false],
            ['valid_user', false],
            ['admin_user', true],
            ['super_admin_user', true],
        ];
    }

    public function testContentReadPageExistsUnauthenticated(): void
    {
        $this->doReadPageExistsTest('index');
    }

    public function testContentReadPageExistsNullTitleUnauthenticated(): void
    {
        $this->doReadPageExistsTest('null_title', true);
    }

    /**
     * @dataProvider providerUsername
     */
    public function testContentReadPageExistsAuthenticated(string $username): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $this->assertNotNull($user);

        $this->doReadPageExistsTest('index', false, $user);
    }

    public function testContentReadPageDoesNotExist(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->client->request('GET', self::CONTENT_URL.'invalid-page');
    }

    public function testContentUpdatePageExistsUnauthenticated(): void
    {
        $this->expectException(AccessDeniedException::class);

        $page = 'index';
        $title = 'foo';
        $content = 'bar';
        $updateDto = new UpdatePageContentRequest($page, $title, $content);

        $this->makeApiRequest('PUT', self::CONTENT_URL, null, $updateDto);
    }

    /**
     * @dataProvider providerUsernameUpdateStatus
     */
    public function testContentUpdatePageExistsAuthenticated(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $this->assertNotNull($user);
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        }

        $page = 'index';
        $title = bin2hex(random_bytes(16));
        $content = bin2hex(random_bytes(16));
        $updateDto = new UpdatePageContentRequest($page, $title, $content);

        $this->makeApiRequest('PUT', self::CONTENT_URL, null, $updateDto, $user);

        if (!$expectAccessGranted) {
            return;
        }

        $this->doPageExistsAssertions(false);
        $jsonData = (array) json_decode((string) $this->client->getResponse()->getContent(), true);

        $this->assertSame($page, $jsonData['id']);
        $this->assertSame($title, $jsonData['title']);
        $this->assertSame($content, $jsonData['content']);

        $modifiedAt = new \DateTimeImmutable((string) $jsonData['modifiedAt']);
        $this->assertEqualsWithDelta((new \DateTimeImmutable())->getTimestamp(), $modifiedAt->getTimestamp(), 2);
    }

    public function testContentUpdatePageDoesNotExistUnauthenticated(): void
    {
        $this->expectException(AccessDeniedException::class);

        $page = 'invalid-page';
        $title = 'foo';
        $content = 'bar';
        $updateDto = new UpdatePageContentRequest($page, $title, $content);

        $this->makeApiRequest('PUT', self::CONTENT_URL, null, $updateDto);
    }

    /**
     * @dataProvider providerUsernameUpdateStatus
     */
    public function testContentUpdatePageDoesNotExistAuthenticated(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $this->assertNotNull($user);
        $this->expectException($expectAccessGranted ? NotFoundHttpException::class : AccessDeniedException::class);

        $page = 'invalid-page';
        $title = 'foo';
        $content = 'bar';
        $updateDto = new UpdatePageContentRequest($page, $title, $content);

        $this->makeApiRequest('PUT', self::CONTENT_URL, null, $updateDto, $user);
    }

    private function doPageExistsAssertions(bool $isTitleNull): void
    {
        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());
        $jsonData = (array) json_decode((string) $response->getContent(), true);

        $this->assertArrayHasKey('id', $jsonData);
        $this->assertArrayHasKey('title', $jsonData);
        $this->assertArrayHasKey('content', $jsonData);
        $this->assertArrayHasKey('modifiedAt', $jsonData);

        $this->assertIsString($jsonData['id']);
        $this->assertNotEmpty($jsonData['id']);

        if ($isTitleNull) {
            $this->assertNull($jsonData['title']);
        } else {
            $this->assertIsString($jsonData['title']);
            $this->assertNotEmpty($jsonData['title']);
        }

        $this->assertIsString($jsonData['content']);
        $this->assertNotEmpty($jsonData['content']);

        $this->assertIsString($jsonData['modifiedAt']);
        $this->assertNotEmpty($jsonData['modifiedAt']);
    }

    private function doReadPageExistsTest(string $page, bool $isTitleNull = false, ?User $user = null): void
    {
        $this->makeApiRequest('GET', self::CONTENT_URL.$page, null, null, $user);

        $this->doPageExistsAssertions($isTitleNull);
    }
}
