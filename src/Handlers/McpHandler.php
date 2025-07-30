<?php

namespace willitscale\Streetlamp\Ai\Handlers;

use DI\Container;
use Psr\Http\Message\ResponseInterface;
use willitscale\Streetlamp\Ai\Models\Capability;
use willitscale\Streetlamp\Attributes\Parameter\BodyParameter;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Models\JsonRpc\Request;
use willitscale\Streetlamp\Models\JsonRpc\Response;
use willitscale\Streetlamp\Models\Route;
use willitscale\Streetlamp\Models\RouteState;

class McpHandler
{
    public function __construct(
        private RouteState $routeState,
        private Route $route
    ) {
    }

    public function call(
        #[BodyParameter] Request $request
    ): ResponseInterface {
        return $this->handshake($request);
    }

    public function handshake(Request $request): ResponseInterface
    {
        $capabilities = [];
        $routingClass = $this->route->getAttribute('class');
        $alias = $this->route->getAttribute('alias');

        foreach ($this->routeState->getAttributes() as $attribute) {
            if (
                $attribute instanceof Capability &&
                ($attribute->getClass() === $routingClass ||
                    ($attribute->getAlias() === $alias && !is_null($alias))) &&
                $attribute->getClass() !== self::class
            ) {
                $capabilities [] = $attribute->getType()->value;
            }
        }

        $response = new Response(
            $request->getJsonRpc(),
            $request->getId(),
            $capabilities
        );

        return new ResponseBuilder()
            ->setData($response)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->build();
    }
}
