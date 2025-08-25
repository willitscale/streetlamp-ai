<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Handlers;

use DI\Container;
use Exception;
use Psr\Http\Message\ResponseInterface;
use stdClass;
use willitscale\Streetlamp\Ai\Attributes\McpSession;
use willitscale\Streetlamp\Ai\Attributes\McpStream;
use willitscale\Streetlamp\Ai\Enums\McpCapabilities;
use willitscale\Streetlamp\Ai\Enums\McpVersion;
use willitscale\Streetlamp\Ai\Models\Capability;
use willitscale\Streetlamp\Ai\Requests\McpRequest;
use willitscale\Streetlamp\Attributes\Parameter\BodyParameter;
use willitscale\Streetlamp\Attributes\Parameter\HeaderParameter;
use willitscale\Streetlamp\Attributes\Validators\RegExpValidator;
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
    private McpSessionHandler $sessionHandler;

    public function __construct(
        private RouteState $routeState,
        private Route $route,
        private Container $container
    ) {
        $this->sessionHandler = $this->sessionHandler ?? $this->getSessionHandler();
    }

    private function getSessionHandler(): McpSessionHandler
    {
        $sessionHandler = array_find(
            $this->routeState->getAttributes(),
            fn($attr) => $attr instanceof McpSession
        );

        $sessionHandler = $this->container->make(
            $sessionHandler->getClass() ?? McpSessionFileHandler::class
        );

        $this->container->set(McpSessionHandler::class, $sessionHandler);

        return $sessionHandler;
    }

    public function delete(
        #[HeaderParameter('MCP-Session-Id')] #[RegExpValidator('/^[a-z0-9\._]+$/i')] ?string $mcpSessionId = null
    ): ResponseInterface {
        $this->sessionHandler->delete($mcpSessionId);
        return new ResponseBuilder()
            ->setHttpStatusCode(HttpStatusCode::HTTP_NO_CONTENT)
            ->build();
    }

    public function stream(
        #[HeaderParameter('MCP-Protocol-Version', false)] string $mcpProtocolVersion = McpVersion::LATEST->value,
        #[HeaderParameter('MCP-Session-Id', false)] #[RegExpValidator('/^[a-z0-9\._]+$/i')] ?string $mcpSessionId = null,
        #[HeaderParameter('Last-Event-ID', false)] ?string $lastEventId = null
    ): ResponseInterface {
        $stream = array_find(
            $this->routeState->getAttributes(),
            fn($attr) => $attr instanceof McpStream
        );

        $mcpSessionId = $mcpSessionId ?? $this->sessionHandler->getSessionId();

        $stream = $this->container->make(
            $stream->getClass(),
            [
                'mcpProtocolVersion' => $mcpProtocolVersion,
                'mcpSessionId' => $mcpSessionId,
            ]
        );

        if (!$stream instanceof ServerSentEventsDispatcher) {
            throw new Exception('Stream handler must implement ServerSentEventsDispatcher');
        }

        return new ResponseBuilder()
            ->setStreamDispatcher($stream)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::TEXT_EVENT_STREAM)
            ->addHeader('MCP-Session-Id', $mcpSessionId)
            ->build();
    }

    public function call(
        #[BodyParameter] Request $request,
        #[HeaderParameter('Accept', true)] string $accept,
        #[HeaderParameter('MCP-Protocol-Version', false)] string $mcpProtocolVersion = McpVersion::LATEST->value,
        #[HeaderParameter('MCP-Session-Id')] #[RegExpValidator('/^[a-z0-9\._]+$/i')] ?string $mcpSessionId = null
    ): ResponseInterface {
        list($method, $action) = array_merge(
            explode('/', $request->getMethod(), 2),
            [null, null]
        );

        $this->sessionHandler->start($mcpSessionId);

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
                ->addHeader('MCP-Session-Id', $this->sessionHandler->getSessionId())
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
            ->addHeader('MCP-Session-Id', $this->sessionHandler->getSessionId())
            ->build();
    }

    public function ping(): object
    {
        return (object) null;
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
            ->addHeader('MCP-Session-Id', $this->sessionHandler->getSessionId())
            ->build();
    }
}
