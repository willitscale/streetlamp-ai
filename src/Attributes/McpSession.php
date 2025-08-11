<?php

namespace willitscale\Streetlamp\Ai\Attributes;

use Attribute;
use willitscale\Streetlamp\Attributes\AttributeClass;
use willitscale\Streetlamp\Attributes\AttributeContract;
use willitscale\Streetlamp\Models\RouteState;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class McpSession implements AttributeContract
{
    public function __construct(private string $class)
    {
    }

    public function bind(
        RouteState $routeState,
        AttributeClass $attributeClass,
        ?string $method = null
    ): void {
        $routeState->addAttribute(
            [
                'type' => self::class,
                'class' => $this->class,
            ]
        );
    }
}
