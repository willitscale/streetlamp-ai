<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Capabilities;

use willitscale\Streetlamp\Ai\Attributes\McpAction;
use willitscale\Streetlamp\Ai\Attributes\McpCapability;
use willitscale\Streetlamp\Ai\Enums\McpCapabilities;

#[McpCapability(McpCapabilities::COMPLETIONS)]
class CompletionsCapability
{
    #[McpAction('complete')]
    public function complete(): array
    {
        return [
            "completion" => [
                "values" => ["python", "pytorch", "pyside"],
                "total" => 10,
                "hasMore" => true
            ]
        ];
    }
}
