<?php

declare(strict_types=1);

namespace App\Tests\unit\EventSubscriber;

use App\EventSubscriber\JsonValidationExceptionSubscriber;
use App\Exception\JsonValidation\JsonValidationException;
use Opis\JsonSchema\Errors\ValidationError;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class JsonValidationExceptionSubscriberTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $eventsList = JsonValidationExceptionSubscriber::getSubscribedEvents();
        $this->assertArrayHasKey(KernelEvents::EXCEPTION, $eventsList);
        $this->assertIsArray($eventsList[KernelEvents::EXCEPTION]);
        $this->assertCount(1, $eventsList[KernelEvents::EXCEPTION]);
    }

    public function testOnKernelExceptionOnlyHandlesJsonValidationException(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $throwable = new \RuntimeException();

        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::SUB_REQUEST, $throwable);
        (new JsonValidationExceptionSubscriber())->onKernelException($event);

        $this->assertNull($event->getResponse());
    }

    public function testOnKernelExceptionOnlyHandlesJsonValidationExceptionWithError(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $error = $this->createMock(ValidationError::class);
        $throwable = (new JsonValidationException())->setError($error);

        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::SUB_REQUEST, $throwable);
        (new JsonValidationExceptionSubscriber())->onKernelException($event);

        $this->assertNotNull($event->getResponse());
        $this->assertInstanceOf(JsonResponse::class, $event->getResponse());
    }
}
