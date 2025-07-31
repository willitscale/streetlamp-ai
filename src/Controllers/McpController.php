<?php

namespace willitscale\Streetlamp\Ai\Controllers;

use willitscale\Streetlamp\Ai\Attributes\McpCapability;
use willitscale\Streetlamp\Ai\Attributes\ModelContextProtocol;
use willitscale\Streetlamp\Ai\Enums\McpCapabilities;
use willitscale\Streetlamp\Ai\Enums\McpVersion;
use willitscale\Streetlamp\Attributes\Middleware;

#[ModelContextProtocol('/mcp', McpVersion::LATEST, 'Streetlamp', '1.0.0', 'Client information')]
#[Middleware(AuthMiddleware::class)]
class McpController
{
    #[McpCapability(McpCapabilities::RESOURCES, true)]
    public function resources(): void
    {
    }

    #[McpCapability(McpCapabilities::PROMPTS, true)]
    public function prompts(): void
    {
    }
}
