<?php

namespace willitscale\Streetlamp\Ai\Models;

use willitscale\Streetlamp\Ai\Enums\McpCapabilities;

readonly class Capability
{
    public function __construct(
        private string $class,
        private string $method,
        private McpCapabilities $type,
        private ?string $alias = null,
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function getType(): McpCapabilities
    {
        return $this->type;
    }
}
