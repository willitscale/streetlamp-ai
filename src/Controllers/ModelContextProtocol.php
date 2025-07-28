<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Controllers;

use Attribute;
use ReflectionClass;
use willitscale\Streetlamp\Ai\Controllers\McpHandler;
use willitscale\Streetlamp\Ai\Controllers\McpVersion;
use willitscale\Streetlamp\Attributes\AttributeClass;
use willitscale\Streetlamp\Attributes\AttributeContract;
use willitscale\Streetlamp\Attributes\RouteContract;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Exceptions\MethodParameterNotMappedException;
use willitscale\Streetlamp\Models\Route;
use willitscale\Streetlamp\Models\RouteState;
use willitscale\Streetlamp\Traits\BuildMethodParameters;
use willitscale\Streetlamp\Traits\BuildMethodRoutes;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class ModelContextProtocol implements AttributeContract
{
    use BuildMethodParameters;
    use BuildMethodRoutes;

    public function __construct(
        private string $path,
        private McpVersion $version = McpVersion::LATEST,
        private ?string $alias = null,
    ) {
    }

    public function bind(
        RouteState $routeState,
        AttributeClass $attributeClass,
        ?string $method = null
    ): void {
        $reflectionClass = $attributeClass->getReflection();
        $isRoutingClass = $reflectionClass->isSubclassOf(McpHandler::class);
        $routingClass = $isRoutingClass ? $reflectionClass->getName() : McpHandler::class;

        $route = new Route(
            $routingClass,
            'call',
            $this->path,
            HttpMethod::POST,
            MediaType::APPLICATION_JSON->value,
        );

        $route->addAttribute('class', $reflectionClass->getName());
        $route->addAttribute('alias', $this->alias);

        foreach ($attributeClass->getAttributes() as $attribute) {
            if (self::class === $attribute->getName()) {
                continue;
            }
            $instance = $attribute->newInstance();
            if ($instance instanceof RouteContract) {
                $instance->applyToRoute($route);
            } elseif ($instance instanceof AttributeContract) {
                $instance->bind($routeState, $attributeClass);
            }
        }

        foreach ($attributeClass->getReflection()->getMethods() as $method) {
            foreach ($method->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                if ($instance instanceof RouteContract) {
                    $instance->applyToRoute($route);
                } elseif ($instance instanceof AttributeContract) {
                    $instance->bind($routeState, $attributeClass, $method->getName());
                }
            }
        }

        $parameters = ($isRoutingClass ? $reflectionClass : new ReflectionClass(McpHandler::class))
            ->getMethod('call')
            ->getParameters();

        foreach ($parameters as $parameter) {
            try {
                $this->buildMethodParameters($route, $parameter);
            } catch (MethodParameterNotMappedException $e) {
            }
        }

        $routeState->addRoute($route);
    }
}
