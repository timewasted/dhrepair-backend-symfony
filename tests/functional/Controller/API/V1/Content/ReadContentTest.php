<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Content;

use App\DTO\ReadPageContentResponse;
use App\Entity\PageContent;
use App\Entity\User;
use App\Repository\PageContentRepository;
use App\Repository\UserRepository;
use App\Tests\traits\ApiRequestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ReadContentTest extends WebTestCase
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

    public function testReadUnauthenticatedPageExists(): void
    {
        $page = 'index';
        $content = $this->contentRepository->find($page);
        $this->assertNotNull($content);
        $dto = new ReadPageContentResponse($content);

        $this->doContentReadTest($page, null, $dto);
    }

    public function testReadUnauthenticatedPageExistsNullTitle(): void
    {
        $page = 'null_title';
        $content = $this->contentRepository->find($page);
        $this->assertNotNull($content);
        $dto = new ReadPageContentResponse($content);

        $this->doContentReadTest($page, null, $dto);
    }

    public function testReadUnauthenticatedPageDoesNotExist(): void
    {
        $this->client->request('GET', self::CONTENT_URL.'invalid-page-123');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @dataProvider providerUsername
     */
    public function testReadAuthenticatedPageExists(string $username): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $page = 'index';
        $content = $this->contentRepository->find($page);
        $this->assertNotNull($content);
        $dto = new ReadPageContentResponse($content);

        $this->doContentReadTest($page, $user, $dto);
    }

    /**
     * @dataProvider providerUsername
     */
    public function testReadAuthenticatedPageDoesNotExist(string $username): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $this->makeApiRequest('GET', self::CONTENT_URL.'invalid-page-123', null, null, $user);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    private function doContentReadTest(string $page, ?User $user, ReadPageContentResponse $dto): void
    {
        $this->makeApiRequest('GET', self::CONTENT_URL.$page, null, null, $user);

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());
        $jsonData = (array) json_decode((string) $response->getContent(), true);

        $this->assertSame($dto->jsonSerialize(), $jsonData);
    }
}
