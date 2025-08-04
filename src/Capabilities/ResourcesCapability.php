<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Capabilities;

use willitscale\Streetlamp\Ai\Attributes\McpCapability;
use willitscale\Streetlamp\Ai\Attributes\McpAction;
use willitscale\Streetlamp\Ai\Attributes\McpSubCapability;
use willitscale\Streetlamp\Ai\Enums\McpCapabilities;
use willitscale\Streetlamp\Ai\Enums\McpSubCapabilities;

#[McpCapability(McpCapabilities::RESOURCES)]
class ResourcesCapability
{
    #[McpSubCapability(McpSubCapabilities::SUBSCRIBE)]
    public function subscribe(): mixed
    {
        return null;
    }

    #[McpAction('list')]
    public function resources(): array
    {
        return [
            'resources' => array_values(array_filter(
                array_map(
                    fn($file) => in_array($file, ['.', '..']) ? null :
                        [
                            "uri" => "file://" . __DIR__ . "/$file",
                            "name" => $file,
                        ],
                    scandir(__DIR__)
                )
            ))
        ];
    }
}
