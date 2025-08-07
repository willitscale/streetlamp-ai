<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Handlers;

use DI\Container;
use Psr\Http\Message\ResponseInterface;
use stdClass;
use willitscale\Streetlamp\Ai\Enums\McpCapabilities;
use willitscale\Streetlamp\Ai\Enums\McpVersion;
use willitscale\Streetlamp\Ai\Models\Capability;
use willitscale\Streetlamp\Ai\Requests\McpRequest;
use willitscale\Streetlamp\Attributes\Parameter\BodyParameter;
use willitscale\Streetlamp\Attributes\Parameter\HeaderParameter;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Models\JsonRpc\Request;
use willitscale\Streetlamp\Models\JsonRpc\Response;
use willitscale\Streetlamp\Models\Route;
use willitscale\Streetlamp\Models\RouteState;
use willitscale\Streetlamp\Responses\ServerSentEventsDispatcher;

class McpHandler
{

    public function __construct(
        private RouteState $routeState,
        private Route $route,
        private Container $container,
    ) {
    }

    public function call(
        #[BodyParameter] Request $request,
        #[HeaderParameter('Accept', true)] string $accept,
        #[HeaderParameter('MCP-Protocol-Version', false)] string $mcpProtocolVersion = McpVersion::LATEST->value,
        #[HeaderParameter('MCP-Session-Id')] ?string $mcpSessionId = null
    ): ResponseInterface {
        list($method, $action) = array_merge(
            explode('/', $request->getMethod(), 2),
            [null, null]
        );

        $this->container->set(
            Request::class,
            $request
        );

        $this->container->set(
            McpRequest::class,
            new McpRequest(
                $method,
                $action,
                $accept,
                $mcpProtocolVersion,
                $mcpSessionId,
            )
        );

        $method = ('completion' === $method) ? 'completions' : $method;

        $invoke = match ($method) {
            'initialize' => [$this, 'initialize'],
            'notifications' => [$this, 'notifications'],
            'ping' => [$this, 'ping'],
            McpCapabilities::PROMPTS->value,
            McpCapabilities::RESOURCES->value,
            McpCapabilities::TOOLS->value,
            McpCapabilities::LOGGING->value,
            McpCapabilities::EXPERIMENTAL->value,
            McpCapabilities::COMPLETIONS->value => $this->capability($method, $action),
        };

        $response = $this->container->call($invoke);

        if ($response instanceof ResponseInterface) {
            return $response;
        }

        if ($response instanceof ServerSentEventsDispatcher) {
            return new ResponseBuilder()
                ->setStreamDispatcher($response)
                ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
                ->setContentType(MediaType::TEXT_EVENT_STREAM)
                ->addHeader('MCP-Session-Id', $mcpSessionId ?? $this->generateSessionId())
                ->build();
        }

        return new ResponseBuilder()
            ->setData(new Response(
                $request->getJsonRpc(),
                $request->getId(),
                $response
            ))
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->addHeader('MCP-Session-Id', $mcpSessionId ?? $this->generateSessionId())
            ->build();
    }

    public function ping(): stdClass
    {
        return new stdClass();
    }

    private function capability(string $method, ?string $action = null): mixed
    {
        $capability = array_find(
            $this->routeState->getAttributes(),
            fn($attr) => $attr instanceof Capability && $attr->getType() === McpCapabilities::from($method)
        );

        $capabilityClass = $this->container->make($capability->getClass());

        return [
            $capabilityClass,
            $capability->getAction($action)
        ];
    }

    public function initialize(
        Request $request,
        McpRequest $mcpRequest,
    ): mixed {
        $capabilities = [];
        $alias = $this->route->getAttribute('alias');

        foreach ($this->routeState->getAttributes() as $attribute) {
            if (
                $attribute instanceof Capability &&
                ($attribute->getAlias() === $alias || is_null($alias)) &&
                $attribute->getClass() !== self::class
            ) {
                $subCapabilities = new stdClass();
                foreach ($attribute->getSubCapabilities() as $subCapability) {
                    $subCapabilities->{$subCapability} = true;
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

        return $result;
    }

    public function notifications(
        Request $request,
        McpRequest $mcpRequest,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setHttpStatusCode(HttpStatusCode::HTTP_NO_CONTENT)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->addHeader('MCP-Session-Id', $mcpRequest->getSessionId())
            ->build();
    }

    private function generateSessionId(): string
    {
        return uniqid('mcp_', true);
    }
}
