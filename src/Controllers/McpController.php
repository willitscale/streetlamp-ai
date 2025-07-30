<?php

namespace willitscale\Streetlamp\Ai\Controllers;

use willitscale\Streetlamp\Ai\Attributes\McpCapability;
use willitscale\Streetlamp\Ai\Enums\McpCapabilities;
use willitscale\Streetlamp\Attributes\Middleware;

#[ModelContextProtocol('/mcp')]
#[Middleware(AuthMiddleware::class)]
class McpController
{
    #[McpCapability(McpCapabilities::RESOURCES)]
    public function resources(): void
    {
    }

    #[McpCapability(McpCapabilities::PROMPTS)]
    public function prompts(): void
    {
    }
}
