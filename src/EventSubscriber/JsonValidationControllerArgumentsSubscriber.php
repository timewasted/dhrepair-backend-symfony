<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Attribute\JsonValidation;
use App\Service\JsonValidationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;

readonly class JsonValidationControllerArgumentsSubscriber implements EventSubscriberInterface
{
    public function __construct(private JsonValidationService $jsonValidationService)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [ControllerArgumentsEvent::class => ['onControllerArguments', 5]];
    }

    public function onControllerArguments(ControllerArgumentsEvent $event): void
    {
        $attributes = $event->getAttributes();
        if (!isset($attributes[JsonValidation::class])) {
            return;
        }
        /** @var JsonValidation[] $jsonValidationAttributes */
        $jsonValidationAttributes = $attributes[JsonValidation::class];
        foreach ($jsonValidationAttributes as $attribute) {
            $this->jsonValidationService->validate(
                $event->getRequest(),
                $attribute->getSchema(),
                $attribute->getDataPath(),
                $attribute->getGlobals(),
                $attribute->getSlots()
            );
        }
    }
}
