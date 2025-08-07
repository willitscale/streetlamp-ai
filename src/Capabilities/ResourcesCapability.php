<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Capabilities;

use Psr\Http\Message\ResponseInterface;
use stdClass;
use willitscale\Streetlamp\Ai\Attributes\McpCapability;
use willitscale\Streetlamp\Ai\Attributes\McpAction;
use willitscale\Streetlamp\Ai\Attributes\McpSubCapability;
use willitscale\Streetlamp\Ai\Enums\McpCapabilities;
use willitscale\Streetlamp\Ai\Enums\McpSubCapabilities;
use willitscale\Streetlamp\Ai\Requests\McpRequest;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Models\JsonRpc\Request;
use willitscale\Streetlamp\Models\JsonRpc\Response;
use willitscale\Streetlamp\Models\ServerSentEvents\Data;
use willitscale\Streetlamp\Models\ServerSentEvents\Event;
use willitscale\Streetlamp\Models\ServerSentEvents\Id;
use willitscale\Streetlamp\Responses\ServerSentEventsDispatcher;

#[McpCapability(McpCapabilities::RESOURCES)]
class ResourcesCapability
{
    #[McpSubCapability(McpSubCapabilities::SUBSCRIBE)]
    public function subscribe(): mixed
    {
        return null;
    }

    #[McpAction('subscribe')]
    public function subscribeResource(Request $request): object
    {
        return new stdClass();
    }

    #[McpAction('unsubscribe')]
    public function unsubscribeResource(Request $request): object
    {
        return new stdClass();
    }

    #[McpAction('list')]
    public function listResources(Request $request): array
    {
        $cursor = $request->getParams()->cursor ?? null;

        return [
            "resources" => [
                [
                    "uri" => "file:///project/src/main.rs",
                    "name" => "main.rs",
                    "title" => "Rust Software Application Main File",
                    "description" => "Primary application entry point",
                    "mimeType" => "text/x-rust"
                ]
            ],
            "nextCursor" => "next-page-cursor"
        ];
    }

    #[McpAction('read')]
    public function readResource(Request $request): array
    {
        return [
            'contents' => [
                [
                    "uri" => $request->getParams()->uri,
                    "name" => "main.rs",
                    "title" => "Rust Software Application Main File",
                    "mimeType" => "text/x-rust",
                    "text" => "fn main() {\n    println!(\"Hello world!\");\n}"
                ]
            ]
        ];
    }

    #[McpAction('templates/list')]
    public function templatesListResources(Request $request): array
    {
        $cursor = $request->getParams()->cursor ?? null;

        return [
            "resourceTemplates" => [
                [
                    "uriTemplate" => "file:///{path}",
                    "name" => "Project Files",
                    "title" => "📁 Project Files",
                    "description" => "Access files in the project directory",
                    "mimeType" => "application/octet-stream"
                ]
            ],
            "nextCursor" => "next-page-cursor"
        ];
    }
}
