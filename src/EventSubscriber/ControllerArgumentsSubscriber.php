<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Attribute\JsonValidation;
use App\Exception\JsonValidation\JsonValidationException;
use Opis\JsonSchema\Validator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;

readonly class ControllerArgumentsSubscriber implements EventSubscriberInterface
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
            try {
                $validation
                    ->setRequest($event->getRequest())
                    ->setSchemaPrefix($this->schemaPrefix ?? '')
                    ->setValidator($this->validator)
                    ->validate()
                ;
            } catch (JsonValidationException $e) {
                throw new BadRequestException('Request does not match the specified JSON schema', 0, $e);
            }
        }
    }
}
