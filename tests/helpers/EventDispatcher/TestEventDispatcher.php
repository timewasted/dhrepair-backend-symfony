<?php

declare(strict_types=1);

namespace App\Tests\helpers\EventDispatcher;

use Symfony\Component\EventDispatcher\EventDispatcher;

class TestEventDispatcher extends EventDispatcher
{
    /**
     * @var object[]
     */
    private array $eventsDispatched = [];

    public function __construct(private bool $dispatchEvents = false)
    {
        parent::__construct();
    }

    public function dispatch(object $event, ?string $eventName = null): object
    {
        $this->eventsDispatched[] = $event;

        return $this->dispatchEvents ? parent::dispatch($event, $eventName) : $event;
    }

    public function eventDispatched(string $className): bool
    {
        foreach ($this->eventsDispatched as $event) {
            if ($event instanceof $className) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return object[]
     */
    public function getEvents(?string $className = null): array
    {
        if (null === $className) {
            return $this->eventsDispatched;
        }

        $events = [];
        foreach ($this->eventsDispatched as $event) {
            if ($event instanceof $className) {
                $events[] = $event;
            }
        }

        return $events;
    }

    public function setDispatchEvents(bool $dispatchEvents): static
    {
        $this->dispatchEvents = $dispatchEvents;

        return $this;
    }
}
