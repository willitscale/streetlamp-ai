<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Models;

use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
readonly class Tool
{
    public function __construct(
        #[JsonProperty(true)] private string $name,
        #[JsonProperty(false)] private ?string $title = null,
        #[JsonProperty(false)] private ?string $description = null,
        #[JsonProperty(false)] private ?Schema $inputSchema = null,
        #[JsonProperty(false)] private ?Schema $outputSchema = null,
        #[JsonProperty(false)] private ?ToolAnnotations $annotations = null,
        #[JsonProperty(false, '_meta')] private ?array $meta = null,
    ) {
    }
}
