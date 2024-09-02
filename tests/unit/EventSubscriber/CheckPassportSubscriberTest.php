<?php

declare(strict_types=1);

namespace App\Tests\unit\EventSubscriber;

use App\EventSubscriber\CheckPassportSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

class CheckPassportSubscriberTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $eventsList = CheckPassportSubscriber::getSubscribedEvents();
        $this->assertArrayHasKey(CheckPassportEvent::class, $eventsList);
        $this->assertIsArray($eventsList[CheckPassportEvent::class]);
        $this->assertCount(2, $eventsList[CheckPassportEvent::class]);
        /** @psalm-suppress PossiblyUndefinedArrayOffset */
        $this->assertLessThan(0, $eventsList[CheckPassportEvent::class][1]);
    }

    public function testOnCheckPassportInvalidUser(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $user = $this->createMock(UserInterface::class);
        $passport = $this->createMock(Passport::class);
        $passport->expects($this->once())->method('getUser')->willReturn($user);
        $event = $this->createMock(CheckPassportEvent::class);
        $event->expects($this->once())->method('getPassport')->willReturn($passport);

        (new CheckPassportSubscriber())->onCheckPassport($event);
    }
}
