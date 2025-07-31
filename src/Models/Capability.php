<?php

namespace willitscale\Streetlamp\Ai\Models;

use willitscale\Streetlamp\Ai\Enums\McpCapabilities;

readonly class Capability
{
    public function __construct(
        private string $class,
        private string $method,
        private McpCapabilities $type,
        private ?bool $listChanged = null,
        private ?bool $subscribe = null,
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

    public function isListChanged(): ?bool
    {
        return $this->listChanged;
    }

    public function isSubscribe(): ?bool
    {
        return $this->subscribe;
    }
}
