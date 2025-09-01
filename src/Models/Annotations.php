<?php

namespace willitscale\Streetlamp\Ai\Models;

use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
readonly class Annotations
{
    public function __construct(
        #[JsonProperty(false)] private ?string $audience = null,
        #[JsonProperty(false)] private ?string $lastModified = null,
        #[JsonProperty(false)] private ?int $priority = null,
    ) {
    }
}
