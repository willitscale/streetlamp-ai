<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Models;

use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonIgnore;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
readonly class Tool
{
    public function __construct(
        #[JsonProperty(true)] private string $name,
        #[JsonProperty(false)] #[JsonIgnore(true)] private ?string $title = null,
        #[JsonProperty(false)] #[JsonIgnore(true)] private ?string $description = null,
        #[JsonProperty(false)] #[JsonIgnore(true)] private ?Schema $inputSchema = null,
        #[JsonProperty(false)] #[JsonIgnore(true)] private ?Schema $outputSchema = null,
        #[JsonProperty(false)] #[JsonIgnore(true)] private ?ToolAnnotations $annotations = null,
        #[JsonProperty(false, '_meta')] #[JsonIgnore(true)] private ?array $meta = null,
    ) {
    }
}
