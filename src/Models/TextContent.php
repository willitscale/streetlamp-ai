<?php

namespace willitscale\Streetlamp\Ai\Models;

use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
class TextContent implements ContentBlock
{
    public function __construct(
        #[JsonProperty(true)] private string $text,
        #[JsonProperty(true)] private string $type,
        #[JsonProperty(false)] private ?Annotations $annotations = null,
        #[JsonProperty(false, '_meta')] private ?array $meta = null
    ) {}
}
