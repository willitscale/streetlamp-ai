<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Models;

use willitscale\Streetlamp\Ai\Enums\McpCapabilities;

class Capability
{
    public function __construct(
        private readonly string $class,
        private readonly McpCapabilities $type,
        private readonly ?string $alias = null,
        private array $subCapabilities = [],
        private array $actions = []
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function getType(): McpCapabilities
    {
        return $this->type;
    }

    public function getSubCapabilities(): array
    {
        return $this->subCapabilities;
    }

    public function addSubCapability(string $subCapability, string $method): void
    {
        $this->subCapabilities[$subCapability] = $method;
    }

    public function getSubCapability(string $subCapability): ?string
    {
        return $this->subCapabilities[$subCapability] ?? null;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function addAction(string $action, string $method): void
    {
        $this->actions[$action] = $method;
    }

    public function getAction(string $action): ?string
    {
        return $this->actions[$action] ?? null;
    }
}
