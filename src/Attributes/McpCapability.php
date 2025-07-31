<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Attributes;

use Attribute;
use willitscale\Streetlamp\Ai\Enums\McpCapabilities;
use willitscale\Streetlamp\Ai\Models\Capability;
use willitscale\Streetlamp\Attributes\AttributeClass;
use willitscale\Streetlamp\Attributes\AttributeContract;
use willitscale\Streetlamp\Models\RouteState;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class McpCapability implements AttributeContract
{
    public function __construct(
        private McpCapabilities $capability,
        private ?bool $listChanged = null,
        private ?bool $subscribe = null,
        private ?string $alias = null,
    ) {
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
                $this->listChanged,
                $this->subscribe,
                $this->alias,
            )
        );
    }
}
