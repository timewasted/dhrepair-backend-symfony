<?php

declare(strict_types=1);

namespace App\Tests\helpers\Service\Stripe;

use Stripe\HttpClient\ClientInterface;
use Stripe\HttpClient\StreamingClientInterface;
use Symfony\Component\HttpFoundation\Response;

class TestStripeHttpClient implements ClientInterface, StreamingClientInterface
{
    private const string PATH_SERVICE_REGEX = '/^\/v1\/([\w]+)\/?/';

    public function request($method, $absUrl, $headers, $params, $hasFile): array
    {
        return $this->handleRequest($method, $absUrl);
    }

    public function requestStream($method, $absUrl, $headers, $params, $hasFile, $readBodyChunkCallable): array
    {
        return $this->handleRequest($method, $absUrl);
    }

    protected function getDefaultResponse(string $method, string $url): array
    {
        return [
            $this->getDefaultResponseBody($method, $url),
            $this->getDefaultResponseStatus($method, $url),
            $this->getDefaultResponseHeaders($method, $url),
        ];
    }

    protected function getDefaultResponseBody(string $method, string $url): string
    {
        return '{}';
    }

    protected function getDefaultResponseHeaders(string $method, string $url): array
    {
        return [];
    }

    protected function getDefaultResponseStatus(string $method, string $url): int
    {
        return Response::HTTP_OK;
    }

    protected function getCustomersResponse(string $method, string $path, ?string $query, ?string $fragment): array
    {
        return [
            $this->getCustomersResponseBody($method, $path),
            $this->getCustomersResponseStatus($method, $path),
            $this->getCustomersResponseHeaders($method, $path),
        ];
    }

    protected function getCustomersResponseBody(string $method, string $url): string
    {
        return json_encode([
            'object' => 'customer',
        ]);
    }

    protected function getCustomersResponseHeaders(string $method, string $url): array
    {
        return $this->getDefaultResponseHeaders($method, $url);
    }

    protected function getCustomersResponseStatus(string $method, string $url): int
    {
        return $this->getDefaultResponseStatus($method, $url);
    }

    protected function getCustomerSessionsResponse(string $method, string $path, ?string $query, ?string $fragment): array
    {
        return [
            $this->getCustomerSessionsResponseBody($method, $path),
            $this->getCustomerSessionsResponseStatus($method, $path),
            $this->getCustomerSessionsResponseHeaders($method, $path),
        ];
    }

    protected function getCustomerSessionsResponseBody(string $method, string $url): string
    {
        return json_encode([
            'object' => 'customer_session',
        ]);
    }

    protected function getCustomerSessionsResponseHeaders(string $method, string $url): array
    {
        return $this->getDefaultResponseHeaders($method, $url);
    }

    protected function getCustomerSessionsResponseStatus(string $method, string $url): int
    {
        return $this->getDefaultResponseStatus($method, $url);
    }

    protected function getPaymentIntentsResponse(string $method, string $path, ?string $query, ?string $fragment): array
    {
        return [
            $this->getPaymentIntentsResponseBody($method, $path),
            $this->getPaymentIntentsResponseStatus($method, $path),
            $this->getPaymentIntentsResponseHeaders($method, $path),
        ];
    }

    protected function getPaymentIntentsResponseBody(string $method, string $url): string
    {
        return json_encode([
            'object' => 'payment_intent',
        ]);
    }

    protected function getPaymentIntentsResponseHeaders(string $method, string $url): array
    {
        return $this->getDefaultResponseHeaders($method, $url);
    }

    protected function getPaymentIntentsResponseStatus(string $method, string $url): int
    {
        return $this->getDefaultResponseStatus($method, $url);
    }

    private function handleRequest(string $method, string $url): array
    {
        $urlPieces = parse_url($url);
        if (!is_array($urlPieces) || !isset($urlPieces['path'])) {
            return $this->getDefaultResponse($method, $url);
        }
        if (1 !== preg_match(self::PATH_SERVICE_REGEX, $urlPieces['path'], $pathPieces)) {
            return $this->getDefaultResponse($method, $url);
        }

        return match ($pathPieces[1]) {
            'customers' => $this->getCustomersResponse(
                $method,
                $urlPieces['path'],
                $urlPieces['query'] ?? null,
                $urlPieces['fragment'] ?? null
            ),
            'customer_sessions' => $this->getCustomerSessionsResponse(
                $method,
                $urlPieces['path'],
                $urlPieces['query'] ?? null,
                $urlPieces['fragment'] ?? null
            ),
            'payment_intents' => $this->getPaymentIntentsResponse(
                $method,
                $urlPieces['path'],
                $urlPieces['query'] ?? null,
                $urlPieces['fragment'] ?? null
            ),
            default => $this->getDefaultResponse($method, $url),
        };
    }
}
