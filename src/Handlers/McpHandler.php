<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Handlers;

use DI\Container;
use Psr\Http\Message\ResponseInterface;
use stdClass;
use willitscale\Streetlamp\Ai\Enums\McpCapabilities;
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
        #[HeaderParameter('MCP-Protocol-Version', true)] string $mcpProtocolVersion,
        #[HeaderParameter('MCP-Session-Id')] ?string $mcpSessionId = null
    ): ResponseInterface {
        list($method, $action) = array_merge(explode('/', $request->getMethod(), 2), [null, null]);

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

        return $this->container->call(
            [
                $this,
                $method
            ]
        );
    }

    public function initialize(
        Request $request,
        McpRequest $mcpRequest,
    ): ResponseInterface {
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

        $response = new Response(
            $request->getJsonRpc(),
            $request->getId(),
            $result
        );

        // TODO: This needs to persist somewhere, maybe session or file cache?
        $mcpSessionId = uniqid('mcp_', true);

        return new ResponseBuilder()
            ->setData($response)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->addHeader('MCP-Session-Id', $mcpSessionId)
            ->build();
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

    public function resources(
        Request $request,
        McpRequest $mcpRequest,
    ): ResponseInterface {
        $capability = array_find(
            $this->routeState->getAttributes(),
            fn($attr) => $attr instanceof Capability && $attr->getType() === McpCapabilities::RESOURCES
        );

        $capabilityClass = $this->container->make($capability->getClass());

        $method = $capability->getAction($mcpRequest->getAction());

        $result = $this->container->call(
            [
                $capabilityClass,
                $method
            ]
        );

        $response = new Response(
            $request->getJsonRpc(),
            $request->getId(),
            $result
        );

        return new ResponseBuilder()
            ->setData($response)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->addHeader('MCP-Session-Id', $mcpRequest->getSessionId())
            ->build();
    }

    public function tools(
        Request $request,
        McpRequest $mcpRequest,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setHttpStatusCode(HttpStatusCode::HTTP_NO_CONTENT)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->addHeader('MCP-Session-Id', $mcpRequest->getSessionId())
            ->build();
    }

    public function prompts(
        Request $request,
        McpRequest $mcpRequest,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setHttpStatusCode(HttpStatusCode::HTTP_NO_CONTENT)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->addHeader('MCP-Session-Id', $mcpRequest->getSessionId())
            ->build();
    }

    public function completions(
        Request $request,
        McpRequest $mcpRequest,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setHttpStatusCode(HttpStatusCode::HTTP_NO_CONTENT)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->addHeader('MCP-Session-Id', $mcpRequest->getSessionId())
            ->build();
    }

    public function experimental(
        Request $request,
        McpRequest $mcpRequest,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setHttpStatusCode(HttpStatusCode::HTTP_NO_CONTENT)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->addHeader('MCP-Session-Id', $mcpRequest->getSessionId())
            ->build();
    }

    public function logging(
        Request $request,
        McpRequest $mcpRequest,
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setHttpStatusCode(HttpStatusCode::HTTP_NO_CONTENT)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->addHeader('MCP-Session-Id', $mcpRequest->getSessionId())
            ->build();
    }
}
