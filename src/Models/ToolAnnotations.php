<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Models;

use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
readonly class ToolAnnotations
{
    public function __construct(
        #[JsonProperty(false)] private ?bool $destructiveHint = null,
        #[JsonProperty(false)] private ?bool $idempotentHint = null,
        #[JsonProperty(false)] private ?bool $openWorldHint = null,
        #[JsonProperty(false)] private ?bool $readOnlyHint = null,
        #[JsonProperty(false)] private ?string $title = null
    ) {
    }
}
