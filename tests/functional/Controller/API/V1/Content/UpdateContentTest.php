<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Content;

use App\DTO\ReadPageContentResponse;
use App\DTO\UpdatePageContentRequest;
use App\Entity\PageContent;
use App\Entity\User;
use App\Repository\PageContentRepository;
use App\Repository\UserRepository;
use App\Tests\traits\ApiRequestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UpdateContentTest extends WebTestCase
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

    public function testUpdateUnauthenticatedPageExists(): void
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
    public function testUpdateAuthenticatedPageExists(string $username, bool $expectAccessGranted): void
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
        $pageContent = $this->contentRepository->find($page);
        $this->assertNotNull($pageContent);

        $this->makeApiRequest('PUT', self::CONTENT_URL, null, $updateDto, $user);

        if (!$expectAccessGranted) {
            return;
        }

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());
        $jsonData = (array) json_decode((string) $response->getContent(), true);

        $readDto = new ReadPageContentResponse($pageContent);
        $this->assertSame($readDto->jsonSerialize(), $jsonData);

        $this->assertSame($pageContent->getTitle(), $updateDto->getTitle());
        $this->assertSame($pageContent->getContent(), $updateDto->getContent());
        $this->assertEqualsWithDelta((new \DateTimeImmutable())->getTimestamp(), (int) $pageContent->getModifiedAt()?->getTimestamp(), 2);
    }

    public function testUpdateUnauthenticatedPageDoesNotExist(): void
    {
        $this->expectException(AccessDeniedException::class);

        $page = 'invalid-page-123';
        $title = 'foo';
        $content = 'bar';
        $updateDto = new UpdatePageContentRequest($page, $title, $content);

        $this->makeApiRequest('PUT', self::CONTENT_URL, null, $updateDto);
    }

    /**
     * @dataProvider providerUsernameUpdateStatus
     */
    public function testUpdateAuthenticatedPageDoesNotExist(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $this->assertNotNull($user);
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        }

        $page = 'invalid-page-123';
        $title = 'foo';
        $content = 'bar';
        $updateDto = new UpdatePageContentRequest($page, $title, $content);

        $this->makeApiRequest('PUT', self::CONTENT_URL, null, $updateDto, $user);

        if ($expectAccessGranted) {
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }
}
