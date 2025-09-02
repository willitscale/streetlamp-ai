<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Models\Results;

use willitscale\Streetlamp\Ai\Models\Tool;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonArray;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonIgnore;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
readonly class ListToolsResult
{
    /**
     * @param Tool[] $tools
     * @param array|null $meta
     * @param string|null $nextCursor
     */
    public function __construct(
        #[JsonArray(Tool::class, true)] private array $tools,
        #[JsonProperty(false, '_meta')] #[JsonIgnore(true)] private ?array $meta = null,
        #[JsonProperty(false)] #[JsonIgnore(true)] private ?string $nextCursor = null
    ) {
    }
}
