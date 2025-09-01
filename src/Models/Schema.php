<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Models;

use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
readonly class Schema
{
    public function __construct(
        #[JsonProperty(true)] private string $type,
        #[JsonProperty(false)] private ?array $properties = null,
        #[JsonProperty(false)] private ?array $required = null
    ) {
    }
}
