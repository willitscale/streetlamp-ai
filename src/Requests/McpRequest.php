<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Requests;

readonly class McpRequest
{
    public function __construct(
        private string $method,
        private ?string $action,
        private string $accept,
        private ?string $protocolVersion,
        private ?string $sessionId
    ) {
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function getAccept(): string
    {
        return $this->accept;
    }

    public function getProtocolVersion(): ?string
    {
        return $this->protocolVersion;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }
}
