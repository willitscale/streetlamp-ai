<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Capabilities;

use willitscale\Streetlamp\Ai\Attributes\McpCapability;
use willitscale\Streetlamp\Ai\Attributes\McpAction;
use willitscale\Streetlamp\Ai\Attributes\McpSubCapability;
use willitscale\Streetlamp\Ai\Enums\McpCapabilities;
use willitscale\Streetlamp\Ai\Enums\McpSubCapabilities;

#[McpCapability(McpCapabilities::PROMPTS)]
class PromptsCapability
{
    #[McpSubCapability(McpSubCapabilities::SUBSCRIBE)]
    public function subscribe(): mixed
    {
        return null;
    }

    #[McpAction('list')]
    public function prompts(): mixed
    {
        return [];
    }
}
