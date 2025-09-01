<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Capabilities;

use willitscale\Streetlamp\Ai\Attributes\McpAction;
use willitscale\Streetlamp\Ai\Attributes\McpCapability;
use willitscale\Streetlamp\Ai\Enums\McpCapabilities;
use willitscale\Streetlamp\Ai\Models\Results\CallToolResult;
use willitscale\Streetlamp\Ai\Models\Schema;
use willitscale\Streetlamp\Ai\Models\Results\ListToolsResult;
use willitscale\Streetlamp\Ai\Models\TextContent;
use willitscale\Streetlamp\Ai\Models\Tool;
use willitscale\Streetlamp\Ai\Models\ToolAnnotations;
use willitscale\Streetlamp\Models\JsonRpc\Request;

#[McpCapability(McpCapabilities::TOOLS)]
class ToolsCapability
{
    #[McpAction('list')]
    public function listTools(Request $request): ListToolsResult
    {
        $cursor = $request->getParams()->cursor ?? null;

        return new ListToolsResult(
            tools: [
                new Tool(
                    name: "get_weather",
                    title: "Weather Information Provider",
                    description: "Get current weather information for a location",
                    inputSchema: new Schema(
                        "object",
                        [
                            "location" => [
                                "type" => "string",
                                "description" => "City name or zip code"
                            ]
                        ],
                        ["location"]
                    )
                )
            ],
            nextCursor: "next-page-cursor"
        );
    }

    #[McpAction('call')]
    public function callTool(Request $request): CallToolResult
    {
        $name = $request->getParams()->name;
        $arguments = $request->getParams()->arguments;

        return new CallToolResult(
            content: [
                new TextContent(
                    text: "Current weather in New York:\nTemperature: 72°F\nConditions: Partly cloudy",
                    type: "text"
                )
            ],
            isError: false
        );
    }
}
