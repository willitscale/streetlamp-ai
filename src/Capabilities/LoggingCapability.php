<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Capabilities;

use stdClass;
use willitscale\Streetlamp\Ai\Attributes\McpAction;
use willitscale\Streetlamp\Ai\Attributes\McpCapability;
use willitscale\Streetlamp\Ai\Enums\McpCapabilities;
use willitscale\Streetlamp\Models\JsonRpc\Request;

#[McpCapability(McpCapabilities::LOGGING)]
class LoggingCapability
{
    #[McpAction('setLevel')]
    public function setLevel(Request $request): object
    {
        return new stdClass();
    }
}
