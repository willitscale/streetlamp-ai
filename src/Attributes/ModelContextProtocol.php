<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Attributes;

use Attribute;
use ReflectionClass;
use willitscale\Streetlamp\Ai\Enums\McpVersion;
use willitscale\Streetlamp\Ai\Handlers\McpHandler;
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
        private ?string $serverName = null,
        private ?string $serverVersion = null,
        private ?string $instructions = null,
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

        $attributes = [
            'class' => $reflectionClass->getName(),
            'serverName' => $this->serverName,
            'serverVersion' => $this->serverVersion,
            'version' => $this->version->value,
            'alias' => $this->alias
        ];

        $reflectionClass = $isRoutingClass ? $reflectionClass : new ReflectionClass(McpHandler::class);

        $this->add(
            $reflectionClass,
            $attributeClass,
            $routeState,
            $routingClass,
            $attributes,
            'call',
        );
    }

    public function add(
        ReflectionClass $reflectionClass,
        AttributeClass $attributeClass,
        RouteState $routeState,
        string $routingClass,
        array $attributes,
        string $callableMethod
    ): void {
        $route = new Route(
            $routingClass,
            $callableMethod,
            $this->path,
            HttpMethod::POST,
            MediaType::APPLICATION_JSON->value,
            [],
            [],
            $attributes
        );

        foreach ($attributeClass->getAttributes() as $attribute) {
            if (self::class === $attribute->getName()) {
                continue;
            }
            $instance = $attribute->newInstance();
            if ($instance instanceof RouteContract) {
                $instance->applyToRoute($route);
            }
        }

        foreach ($attributeClass->getReflection()->getMethods() as $method) {
            foreach ($method->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                if ($instance instanceof RouteContract) {
                    $instance->applyToRoute($route);
                }
            }
        }

        $parameters = $reflectionClass
            ->getMethod($callableMethod)
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
