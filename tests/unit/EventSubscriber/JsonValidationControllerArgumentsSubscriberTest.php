<?php

declare(strict_types=1);

namespace App\Tests\unit\EventSubscriber;

use App\EventSubscriber\JsonValidationControllerArgumentsSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;

class JsonValidationControllerArgumentsSubscriberTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $eventsList = JsonValidationControllerArgumentsSubscriber::getSubscribedEvents();
        $this->assertArrayHasKey(ControllerArgumentsEvent::class, $eventsList);
        $this->assertIsArray($eventsList[ControllerArgumentsEvent::class]);
        $this->assertCount(2, $eventsList[ControllerArgumentsEvent::class]);
        /** @psalm-suppress PossiblyUndefinedArrayOffset */
        $this->assertSame(5, $eventsList[ControllerArgumentsEvent::class][1]);
    }
}
