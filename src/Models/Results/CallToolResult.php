<?php

namespace willitscale\Streetlamp\Ai\Models\Results;

use willitscale\Streetlamp\Ai\Models\ContentBlock;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
readonly class CallToolResult
{
    /**
     * @param ContentBlock[] $content
     * @param bool|null $isError
     * @param array|null $structuredContent
     * @param array|null $meta
     */
    public function __construct(
        #[JsonArray(ContentBlock::class, true)] private array $content,
        #[JsonProperty(false)] private ?bool $isError = null,
        #[JsonProperty(false)] private ?array $structuredContent = null,
        #[JsonProperty(false, '_meta')] private ?array $meta = null
    ) {
    }
}
