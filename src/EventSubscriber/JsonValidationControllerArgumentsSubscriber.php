<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Attribute\JsonValidation;
use Opis\JsonSchema\Validator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;

readonly class JsonValidationControllerArgumentsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Validator $validator,
        private ?string $schemaPrefix = null
    ) {
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
        /** @var JsonValidation[] $jsonValidations */
        $jsonValidations = $attributes[JsonValidation::class];
        foreach ($jsonValidations as $validation) {
            $validation
                ->setRequest($event->getRequest())
                ->setSchemaPrefix($this->schemaPrefix ?? '')
                ->setValidator($this->validator)
                ->validate()
            ;
        }
    }
}
