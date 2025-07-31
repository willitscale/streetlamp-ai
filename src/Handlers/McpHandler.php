<?php

namespace willitscale\Streetlamp\Ai\Handlers;

use Psr\Http\Message\ResponseInterface;
use stdClass;
use willitscale\Streetlamp\Ai\Models\Capability;
use willitscale\Streetlamp\Attributes\Parameter\BodyParameter;
use willitscale\Streetlamp\Attributes\Parameter\HeaderParameter;
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

    public function callHttp(
        #[BodyParameter] Request $request,
        #[HeaderParameter('Mcp-Session-Id')] ?string $mcpSessionId = null,
    ): ResponseInterface {
        // TODO: Better error handling
        return match ($request->getMethod()) {
            'initialize' => $this->initialize($request),
            'notifications/initialized' => $this->initializeNotification($request),
        };
    }

    public function callSse(
        #[BodyParameter] Request $request,
        #[HeaderParameter('Mcp-Session-Id')] ?string $mcpSessionId = null,
    ): ResponseInterface {
        // TODO: SSE support
        return new ResponseBuilder()
            ->setHttpStatusCode(HttpStatusCode::HTTP_NO_CONTENT)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->build();
    }

    public function initialize(Request $request): ResponseInterface
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
                $subCapabilities = new stdClass();

                if ($attribute->isListChanged() !== null) {
                    $subCapabilities->listChanged = $attribute->isListChanged();
                }

                if ($attribute->isSubscribe() !== null) {
                    $subCapabilities->subscribe = $attribute->isSubscribe();
                }

                $capabilities [$attribute->getType()->value] = $subCapabilities;
            }
        }

        $serverInfo = new stdClass();

        if ($this->route->getAttribute('serverName')) {
            $serverInfo->name = $this->route->getAttribute('serverName');
        }
        if ($this->route->getAttribute('serverVersion')) {
            $serverInfo->version = $this->route->getAttribute('serverVersion');
        }

        $result = new stdClass();
        $result->protocolVersion = $this->route->getAttribute('version');
        $result->capabilities = $capabilities;
        $result->serverInfo = $serverInfo;

        if ($this->route->getAttribute('instructions')) {
            $result->instructions = $this->route->getAttribute('instructions');
        }

        $response = new Response(
            $request->getJsonRpc(),
            $request->getId(),
            $result
        );

        // TODO: This needs to persist somewhere, maybe session or file cache?
        $mcpSessionId = uniqid();

        return new ResponseBuilder()
            ->setData($response)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->addHeader('Mcp-Session-Id', $mcpSessionId)
            ->build();
    }

    public function initializeNotification(): ResponseInterface
    {
        return new ResponseBuilder()
            ->setHttpStatusCode(HttpStatusCode::HTTP_NO_CONTENT)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->build();
    }
}
