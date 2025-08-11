<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Capabilities;

use stdClass;
use willitscale\Streetlamp\Ai\Attributes\McpCapability;
use willitscale\Streetlamp\Ai\Attributes\McpAction;
use willitscale\Streetlamp\Ai\Attributes\McpSubCapability;
use willitscale\Streetlamp\Ai\Enums\McpCapabilities;
use willitscale\Streetlamp\Ai\Enums\McpSubCapabilities;
use willitscale\Streetlamp\Models\JsonRpc\Request;

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
                    "uri" => "/product/1234",
                    "name" => "Product 1234",
                    "title" => "My Product",
                    "description" => "This is a product resource",
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
                    "name" => "Product 1234",
                    "title" => "My Product",
                    "text" => "Information about Product 1234",
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
