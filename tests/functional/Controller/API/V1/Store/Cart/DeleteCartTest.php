<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Store\Cart;

use App\DTO\ReadCartResponse;
use App\Entity\User;
use App\Event\BaseCartEvent;
use App\Event\CartDeletedEvent;
use App\Repository\UserRepository;
use App\Tests\helpers\EventDispatcher\TestEventDispatcher;
use App\Tests\traits\ApiRequestTrait;
use App\ValueObject\ShoppingCart;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DeleteCartTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string DELETE_URL = '/api/v1/store/cart';

    private KernelBrowser $client;
    private TestEventDispatcher $eventDispatcher;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        $container = self::getContainer();
        /** @psalm-suppress InvalidPropertyAssignmentValue */
        $this->eventDispatcher = $container->get(EventDispatcherInterface::class);

        $entityManager = $container->get('doctrine')->getManager();
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    /**
     * @return list<array{string, bool}>
     */
    public static function providerUsernameAndEventDispatched(): array
    {
        return [
            ['temporary_user', true],
            ['valid_user', true],
            ['admin_user', true],
            ['super_admin_user', false],
        ];
    }

    public function testDeleteUnauthenticated(): void
    {
        $this->expectException(AccessDeniedException::class);
        $this->makeApiRequest('DELETE', self::DELETE_URL);
    }

    /**
     * @dataProvider providerUsernameAndEventDispatched
     */
    public function testDeleteAuthenticated(string $username, bool $expectEventDispatched): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $this->assertNotNull($user);

        $this->makeApiRequest('DELETE', self::DELETE_URL, null, null, $user);
        $jsonData = $this->getJsonResponseData();

        $shoppingCart = new ShoppingCart($user, []);
        $this->assertSame((new ReadCartResponse($shoppingCart))->jsonSerialize(), $jsonData);

        if ($expectEventDispatched) {
            /** @var CartDeletedEvent[] $events */
            $events = $this->eventDispatcher->getEvents(CartDeletedEvent::class);
            $this->assertCount(1, $events);
            $this->assertEquals($shoppingCart, $events[0]->getCart());
        } else {
            $this->assertFalse($this->eventDispatcher->eventDispatched(CartDeletedEvent::class));
        }
    }

    public function testDeleteAuthenticatedWithEmptyCart(): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => 'super_admin_user']);
        $this->assertNotNull($user);

        $this->makeApiRequest('DELETE', self::DELETE_URL, null, null, $user);
        $jsonData = $this->getJsonResponseData();

        $shoppingCart = new ShoppingCart($user, []);
        $this->assertSame((new ReadCartResponse($shoppingCart))->jsonSerialize(), $jsonData);

        $this->assertFalse($this->eventDispatcher->eventDispatched(BaseCartEvent::class));
    }
}
