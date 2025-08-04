<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Attributes;

use Attribute;
use willitscale\Streetlamp\Ai\Models\Capability;
use willitscale\Streetlamp\Attributes\AttributeClass;
use willitscale\Streetlamp\Attributes\AttributeContract;
use willitscale\Streetlamp\Models\RouteState;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class McpAction implements AttributeContract
{
    public function __construct(
        private string $action
    ) {
    }

    public function bind(
        RouteState $routeState,
        AttributeClass $attributeClass,
        ?string $method = null
    ): void {
        $capability = array_find(
            $routeState->getAttributes(),
            fn($attr) => $attr instanceof Capability &&
                $attr->getClass() === $attributeClass->getNamespace() . $attributeClass->getClass()
        );

        $capability->addAction($this->action, $method);
    }
}
