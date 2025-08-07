<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Capabilities;

use willitscale\Streetlamp\Ai\Attributes\McpAction;
use willitscale\Streetlamp\Ai\Attributes\McpCapability;
use willitscale\Streetlamp\Ai\Enums\McpCapabilities;
use willitscale\Streetlamp\Models\JsonRpc\Request;

#[McpCapability(McpCapabilities::TOOLS)]
class ToolsCapability
{
    #[McpAction('list')]
    public function listTools(Request $request): array
    {
        $cursor = $request->getParams()->cursor ?? null;

        return [
            "tools" => [
                [
                    "name" => "get_weather",
                    "title" => "Weather Information Provider",
                    "description" => "Get current weather information for a location",
                    "inputSchema" => [
                        "type" => "object",
                        "properties" => [
                            "location" => [
                                "type" => "string",
                                "description" => "City name or zip code"
                            ]
                        ],
                        "required" => ["location"]
                    ]
                ]
            ],
            "nextCursor" => "next-page-cursor"
        ];
    }

    #[McpAction('call')]
    public function callTool(Request $request): array
    {
        $name = $request->getParams()->name;
        $arguments = $request->getParams()->arguments;

        return [
            "content" => [
                [
                    "type" => "text",
                    "text" => "Current weather in New York:\nTemperature: 72°F\nConditions: Partly cloudy"
                ]
            ],
            "isError" => false
        ];
    }
}
