<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Controllers;

use Attribute;
use willitscale\Streetlamp\Ai\Controllers\Capability;
use willitscale\Streetlamp\Ai\Controllers\McpCapabilities;
use willitscale\Streetlamp\Attributes\AttributeClass;
use willitscale\Streetlamp\Attributes\AttributeContract;
use willitscale\Streetlamp\Models\RouteState;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class McpCapability implements AttributeContract
{
    public function __construct(
        private McpCapabilities $capability,
        private ?string $alias = null,
    ) {
    }

    public function getCapability(): McpCapabilities
    {
        return $this->capability;
    }

    public function bind(
        RouteState $routeState,
        AttributeClass $attributeClass,
        ?string $method = null
    ): void {
        $routeState->addAttribute(
            new Capability(
                $attributeClass->getNamespace() . $attributeClass->getClass(),
                $method,
                $this->capability,
                $this->alias,
            )
        );
    }
}
