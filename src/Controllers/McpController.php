<?php

namespace willitscale\Streetlamp\Ai\Controllers;

use willitscale\Streetlamp\Ai\Controllers\AuthMiddleware;
use willitscale\Streetlamp\Ai\Controllers\McpCapabilities;
use willitscale\Streetlamp\Ai\Controllers\McpCapability;
use willitscale\Streetlamp\Ai\Controllers\ModelContextProtocol;
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
