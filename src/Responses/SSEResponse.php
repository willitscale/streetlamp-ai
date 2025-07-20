<?php

namespace willitscale\Streetlamp\Ai\Responses;

use Closure;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

readonly class SSEResponse implements ResponseInterface
{
    public function __construct(
        private Closure $callback
    ) {
    }

    public function getProtocolVersion(): string
    {
        return '1.1';
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        return $this;
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function hasHeader(string $name): bool
    {
        return true;
    }

    public function getHeader(string $name): array
    {
        // TODO: Implement getHeader() method.
    }

    public function getHeaderLine(string $name): string
    {
        // TODO: Implement getHeaderLine() method.
    }

    public function withHeader(string $name, $value): MessageInterface
    {
        // TODO: Implement withHeader() method.
    }

    public function withAddedHeader(string $name, $value): MessageInterface
    {
        // TODO: Implement withAddedHeader() method.
    }

    public function withoutHeader(string $name): MessageInterface
    {
        // TODO: Implement withoutHeader() method.
    }

    public function getBody(): StreamInterface
    {
        $this->callback->call($this);
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        // TODO: Implement withBody() method.
    }

    public function getStatusCode(): int
    {
        // TODO: Implement getStatusCode() method.
    }

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        // TODO: Implement withStatus() method.
    }

    public function getReasonPhrase(): string
    {
        // TODO: Implement getReasonPhrase() method.
    }
}