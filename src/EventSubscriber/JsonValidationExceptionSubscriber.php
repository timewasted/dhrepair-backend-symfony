<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\JsonValidation\JsonValidationException;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class JsonValidationExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException'],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!($exception instanceof JsonValidationException) || null === ($error = $exception->getError())) {
            return;
        }

        $errorFormatter = new ErrorFormatter();
        $jsonResponse = new JsonResponse([
            'requestValidation' => $errorFormatter->formatOutput($error, 'basic'),
        ]);

        $event->setResponse($jsonResponse);
    }
}
